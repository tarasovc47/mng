<?php
namespace common\widgets;

use Yii;
use common\components\SiteHelper;
use yii\base\InvalidConfigException;

class LeftMenu extends \yii\bootstrap\Widget
{
	public $items = [];

	public function run(){
		return (!empty($this->items)) ? $this->renderMenu() : false;
	}

	public function renderMenu(){
		$html = "<ul class='nav navbar-nav side-nav'>";
		$html .= '<li>
					<a href="#" class="toggleLeftMenu">
						<i class="fa fa-fw fa-plus-square-o"></i>
						<i class="fa fa-fw fa-minus-square-o"></i>
						<span class="lets-show">Раскрыть все</span>
						<span class="lets-hide">Свернуть все</span>
					</a>
				</li>';

		foreach($this->items as $item){
			if(!isset($item['label'])){
	            throw new InvalidConfigException("У всех пунктов должен быть задан 'label'.");
	        }

			$html .= "<li>";

			$html .= "<a href='";
			$html .= isset($item['url']) ? $item['url'] : "#";
			$html .= "'";
			if(isset($item['items']) && !empty($item['items'])){
				$html .= ' class="has-list" data-toggle="collapse" data-target="#' . SiteHelper::translit(strip_tags($item['label'])) . '"';
			}
			$html .= ">";
			$html .= isset($item['icon']) ? "<i class='" . $item['icon'] . "'></i> " : " ";
			$html .= $item['label'];
			if(isset($item['items']) && !empty($item['items'])){
				$html .= '<i class="fa fa-fw fa-caret-down"></i>';
			}
			$html .= "</a>";

			if(isset($item['items']) && !empty($item['items'])){
				$html .= '<ul id="' . SiteHelper::translit(strip_tags($item['label'])) . '" class="collapse">';
				foreach($item['items'] as $child){
					if(!isset($child['label'])){
			            throw new InvalidConfigException("У всех пунктов должен быть задан 'label'.");
			        }

			        $html .= "<li>";

			        $html .= "<a href='";
			        $html .= isset($child['url']) ? $child['url'] : "#";
			        $html .= "'>";
			        $html .= isset($child['icon']) ? "<i class='" . $child['icon'] . "'></i> " : " ";
					$html .= $child['label'];
			        $html .= "</a>";

			        $html .= "</li>";
				}
				$html .= '</ul>';
			}

			$html .= "</li>";
		}

		$html .= "</ul>";
		return $html;
	}
}
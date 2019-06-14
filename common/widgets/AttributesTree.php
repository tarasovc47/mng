<?php
namespace common\widgets;

use Yii;
use yii\helpers\Html;

class AttributesTree extends \yii\bootstrap\Widget
{
	public $attrs = [];
	public $template;
	public $scenariosViewActive = [];
	public $cover = false;
	public $editAccess;

	public function run(){
		if(!is_array($this->scenariosViewActive)){
            $this->scenariosViewActive = explode(",", $this->scenariosViewActive);
        }

		switch($this->template){
			case "backend/scenarios/form":
				return $this->renderBackendScenariosForm($this->attrs);
			case "backend/scenarios/view":
				return $this->renderBackendScenariosView($this->attrs);
			case "backend/techsup/attributes/index":
				return $this->renderBackendAtributesIndex($this->attrs);
			default:
				return '';
		}
	}

	public function renderBackendScenariosForm($attrs, $children = false, $padding = 0, $parent_id = false){
		$html = "";

		foreach($attrs as $service_id => $service){
			$html .= "<div class='service'>" . $service["name"];
			foreach($service["techs"] as $conn_tech_id => $conn_tech){
				$html .= "<div class='connection-technology'>" . $conn_tech["name"];
				$html .= $this->renderBackendScenariosFormInner($conn_tech['attrs'], false, 15);
				$html .= "</div>";	
			}
			$html .= "</div>";			
		}

		return $html;
	}

	private function renderBackendScenariosFormInner($attrs, $children = false, $padding = 0, $parent_id = false){
		$html = "";

		$classes = "attribute";

		if(!$children){
			$classes .= " show";
		}

		foreach($attrs as $key => $attr){
			$printClasses = $classes;
			$printPadding = $padding;

			if($attr['children']){
				$printClasses .= " has-children";
			}
			else{
				$printPadding += 15;
			}

			$html .= "<div style='padding-left: " . $printPadding . "px;' class='" . $printClasses . "'";
			if($parent_id){
				$html .= " data-parent='" . $parent_id . "'";
			}
			$html .= ">";
			$html .= "<i class='switcher fa fa-plus-square-o'></i>";
			$html .= "<i class='switcher fa fa-minus-square-o'></i> ";
			$html .= "<span class='text'>" . $attr['name'] . "</span>";
			$html .= " <span class='choose' data-attr='" . $attr['id'] . "'>Выбрать</span>";

			if($attr['children'])
				$html .= $this->renderBackendScenariosFormInner($attr['children'], true, 15, $attr['id']);

			$html .= "</div>";			
		}

		return $html;
	}

	public function renderBackendScenariosView($attrs, $padding = 0){
		$html = "";

		foreach($attrs as $key => $attr){
			$classes = "text";
			if(in_array($attr['id'], $this->scenariosViewActive))
				$classes .= " added";

			$html .= "<div style='padding-left: " . $padding . "px;' class='attribute'>";

			$html .= "<span class='" . $classes . "'>" . $attr['name'] . "</span>";

			if($attr['children'])
				$html .= $this->renderBackendScenariosView($attr['children'], 15);

			$html .= "</div>";			
		}

		return $html;
	}

	public function renderBackendAtributesIndex($attrs, $padding = 15){
		$ul_classes = "techsup-attributes-tree__list";

		if($padding == 15){
			$ul_classes .= " first-lvl";
		}

		$html = "<ul class='" . $ul_classes . "'>";
		$classes = "techsup-attributes-tree__attribute";

		foreach($attrs as $key => $attr){
			$html .= "<li class='" . $classes . "' data-id='" . $attr['id'] . "' data-sort='" . $attr['sort'] . "'>";
			$html .= "<div style='padding-left: " . $padding . "px;' class='attribute-name'>" . $attr['name'];
			$html .= "<div class='attribute-actions'>";
			$html .= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/techsup/attributes/view', 'id' => $attr['id']], ['title' => 'Подробнее']);
			$html .= " ";
			if($this->editAccess){
				$html .= Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/techsup/attributes/update', 'id' => $attr['id']], ['title' => 'Редактировать']);
			}
			$html .= "</div>";
			$html .= "</div>";

			if($attr['children']){
				$html .= $this->renderBackendAtributesIndex($attr['children'], $padding + 15);
			}

			$html .= "</li>";
		}

		$html .= "</ul>";

		return $html;
	}
}
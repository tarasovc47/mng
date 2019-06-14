<?php

namespace console\websockets\Applications\controllers;

use common\models\Applications;
use common\models\ApplicationsStatuses;
use common\models\Attributes;
use common\models\Properties;
use common\models\ClientSearch;
use common\models\CasUser;
use common\models\Departments;
use common\models\UsersGroups;
use common\components\SiteHelper;
use common\widgets\Applications as ApplicationsWidget;
use yii\helpers\ArrayHelper;

class ViewerController
{
    private static $instance;
    
	public static function Instance(){
		if(self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

	public function render($data, $user){
		$template = $this->whichTemplate($user);

		$stacks = [];
		foreach($data["ids"] as $id => $view){
			$id = explode("-", $id);
			$stack_id = $id[0];
			$id_spec = $id[1];

			$stacks[$stack_id]["id_spec"][] = $id_spec;
			$stacks[$stack_id]["view"] = $view;
		}

		$attributes = [];
		$properties = [];
		$clients = [];
		$applications = [];
		foreach($stacks as $stack_id => $details){
			$apps = Applications::find()
				->where(["application_stack_id" => $stack_id, "id_spec" => $details['id_spec']])
				->all();

			foreach($apps as $key => $application){
				foreach($application->applicationsEvents as $n => $event){
					if($event->applicationAttributes){
						$attrs = SiteHelper::to_php_array($event->applicationAttributes->attributes);

						foreach($attrs as $attr){
	                        if(!in_array($attr, $attributes)){
	                            $attributes[] = $attr;
	                        }
	                    }
					}

					if($event->applicationProperties){
	                    $props = SiteHelper::to_php_array($event->applicationProperties->properties);

	                    foreach($props as $prop){
	                        if(!in_array($prop, $properties)){
	                            $properties[] = $prop;
	                        }
	                    }
	                }
				}

	            if(!in_array($application->loki_basic_service_id, $clients)){
	                $clients[] = $application->loki_basic_service_id;
	            }
			}

			$applications = ArrayHelper::merge($applications, $apps);
		}

		if(!empty($attributes)){
			$attributes = Attributes::findAll($attributes);
	        $attributes = ArrayHelper::index($attributes, "id");
		}

		if(!empty($properties)){
			$properties = Properties::findAll($properties);
	        $properties = ArrayHelper::index($properties, "id");
		}

		if(!empty($clients)){
            $clientSearch = new ClientSearch();
            $clients = $clientSearch->searchByLokiBasicService($clients);
        }

        // ApplicationsWidget
        $i = 0;
    	$apps = [];
        foreach($stacks as $stack_id => $details){
        	$apps[$i]["stack_id"] = $stack_id;
        	
        	if($apps[$i]["is_full"] = ($details["view"] == "full")){
        		$apps[$i]["stack"] = ApplicationsWidget::stack($stack_id, $applications, $attributes, $properties, $clients, $template, $user);
        	}
        	else{
        		foreach($details["id_spec"] as $application_id_spec){
        			foreach($applications as $application){
        				if($application->id_spec == $application_id_spec){
        					$apps[$i]["apps"][$application->id]['short'] = ApplicationsWidget::short($application, $template, $clients, $attributes);
        					$apps[$i]["apps"][$application->id]['full'] = ApplicationsWidget::application($application, $template, $user, $clients, $attributes, $properties);
        				}
        			}
        		}
        	}

        	$i++;
        }

        $response = [
        	"status" => "render",
        	"applications" => $apps,
        ];

		return $response;
	}

	public function responsible($data, $user){
		$application = Applications::loadApplication($data['application_id']);

        if(!$application){
            return ['status' => "error", 'message' => 'Не удалось определить заявку'];
        }

        $template = $this->whichTemplate($user);
        $list = [];

        switch($template){
        	case "nod":
        		break;
        	case "brigadier":
        		$list = CasUser::loadList([
        			'and', 
        				[ "group_id" => $user->group_id ], 
        				"id != " . $user->id 
        			]);
        		break;
        	default:
        		$list = CasUser::loadList([ "department_id" => $user->department_id ]);
        }

        $attributes = Attributes::loadTreeByTechnologyId($application->connection_technology_id, $user->department_id);
        $html = ApplicationsWidget::confirm_responsible($list, $attributes, $application);

		return [ 'status' => "responsible", 'data' => [ 
			'html' => $html,
			'application_id' => $data["application_id"] 
		]];
	}

	public function department($data, $user){
		$application = Applications::loadApplication($data['application_id']);

        if(!$application){
            return ['status' => "error", 'message' => 'Не удалось определить заявку'];
        }

        $departments = Departments::loadList();

        $brigades = UsersGroups::loadList([ "department_id" => 2 ]); // Служба эксплуатации
        $default_brigade = Applications::identifyBrigade($application->loki_basic_service_id);

        $attributes = Attributes::loadTreeByTechnologyId($application->connection_technology_id, $user->department_id);
        $html = ApplicationsWidget::confirm_department($departments, $attributes, $application, $brigades, $default_brigade);

		return [ 'status' => "department", 'data' => [ 
			'html' => $html,
			'application_id' => $data["application_id"] 
		]];
	}

	public function refuse($data, $user){
		$application = Applications::loadApplication($data['application_id']);

        if(!$application){
            return ['status' => "error", 'message' => 'Не удалось определить заявку'];
        }

        $html = ApplicationsWidget::confirm_refuse($application);        

		return [ 'status' => "refuse", 'data' => [ 
			'html' => $html,
			'application_id' => $data["application_id"] 
		]];
	}

	public function complete($data, $user){
		$application = Applications::loadApplication($data['application_id']);

        if(!$application){
            return ['status' => "error", 'message' => 'Не удалось определить заявку'];
        }

        $template = $this->whichTemplate($user);

        switch($template){
        	case "nod":
        		$statuses = ApplicationsStatuses::loadList([ "id" => [3,4,5,7] ]);
        		break;
        	default:
        		$statuses = [];
        }

        $attributes = Attributes::loadTreeByTechnologyId($application->connection_technology_id, $user->department_id);
        $html = ApplicationsWidget::confirm_complete($attributes, $application, $statuses);

		return [ 'status' => "complete", 'data' => [ 
			'html' => $html,
			'application_id' => $data["application_id"] 
		]];
	}

	public function handle($data, $user){
		$application = Applications::loadApplication($data['application_id']);

        if(!$application){
            return ['status' => "error", 'message' => 'Не удалось определить заявку'];
        }

        $properties = Properties::loadTreeByType($application->application_type_id);
        $html = ApplicationsWidget::confirm_handle($properties, $application);

        return [ 'status' => "handle", 'data' => [ 
			'html' => $html,
			'application_id' => $data["application_id"] 
		]];
	}

	private function whichTemplate($user){
		switch($user->department_id){
			case 2:
                if($user->usersGroup->head_id == $user->id){
                	// Бригадир
                    $template = "brigadier";
                }
                else{
                	// Член бригады
                	$template = "nod";
                }
				break;
			case 3:
				// Инженер сетевых технологий
				$template = "engineer";
				break;
			default:
				$template = "";
		}

		return $template;
	}
}
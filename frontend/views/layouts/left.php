<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <?/* ?><div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?=1//Yii::$app->user->identity->displayname?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <style>


            #anchor-header:before,
            #anchor-header  {
                content: url(/img/icons/hydra_logo.png) ;
                /*background-position: 10px 10px; !* X and Y *!*//*
                vertical-align: middle;
                background-size: contain;
                height: 1.2em;
                width: 1.2em;
            }
        </style>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <?php*/


        $checkModule = function ($route) {
            return $route === Yii::$app->controller->module->id;
        };

        $checkController = function ($route) {
            return $route === $this->context->getUniqueId();
        };

        $checkAction = function ($route) {
            return $route === Yii::$app->controller->action->id;
        };

        echo dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => $menuLeftItems
            ]
        ) ?>

    </section>

</aside>

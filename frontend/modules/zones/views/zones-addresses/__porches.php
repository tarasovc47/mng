<div id="zones__address__porches__panel-group" class="panel-group collapse in" aria-expanded="true" style="">
    <?php 
        if (isset($porches) && !empty($porches)){
            foreach ($porches as $porch_id => $porch_name) {
                echo $this->render('__porch', [
                            'porch_id' => $porch_id,
                            'porch_name' => $porch_name,
                            'parent_div' => 'zones__address__porches__panel-group',
                        ]);
            }
        }     
    ?>
</div>
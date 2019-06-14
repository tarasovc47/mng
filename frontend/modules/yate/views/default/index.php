<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 20.11.18
 * Time: 22:52
 */

?><div class="col-sm-8">
    <ul class="nav nav-pills">
        <li <? //if ($subscriber=='local_user_out'){ echo 'class="active"';}?>><a href="?page=subscribers&subscriber=local_user_out"><span class="izhitsa">Профили абонентов</span></a></li>
        <li <? //if ($subscriber=='cfw'){ echo 'class="active"';}?>><a href="?page=subscribers&subscriber=cfw"><span class="izhitsa">Переадресация</span></a></li>
        <li <? //if ($subscriber=='change_cli'){ echo 'class="active"';}?>><a href="?page=subscribers&subscriber=change_cli"><span class="izhitsa">Изменение АОНа</span></a></li>
        <li <? //if ($subscriber=='block'){ echo 'class="active"';}?>><a href="?page=subscribers&subscriber=block"><span class="izhitsa">Блокировка</span></a></li>
    </ul>
</div>
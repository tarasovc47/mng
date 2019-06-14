<?

use yii\widgets\Breadcrumbs;
echo Breadcrumbs::widget([
    'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
    'links' => [
        [
            'label' => 'IpMon',

            'template' => "<li>{link}<sup>v2</sup></li>\n", // template for this link only
        ],
    ],
]);
?><style>

</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Search <acronym title="Global Biodiversity Information Facility">GBIF</acronym></h3>
    </div>
    <div class="panel-body hidden-print">
        <div class="form-inline">
            <input type="text" name="query" id="query" autocorrect="off" autocomplete="off" placeholder="Enter Search Phrase" class="form-control">
            <button id="btnResetSearch" class="btn btn-default btn-sm">×</button>
            <button id="btnSearch" class="btn btn-default btn-sm">Search</button>
        </div>
        <div class="collapse in" id="searchResultPane" aria-expanded="true">
            <table hidden id="searchResultTree" class="table table-striped table-hover table-condensed table-bordered fancytree-container fancytree-ext-table" tabindex="0">
                <colgroup>
                    <col width="10em">
                    <col width="10em">
                    <col width="*">
                    <col width="30em">
                    <col width="30em">
                    <col width="30em">
                    <col width="30em">
                    <col width="30em">
                    <col width="10em">
                    <col width="10em">
                    <col width="30em">
                    <col width="30em">
                </colgroup>
                <thead>
                <tr>
                    <th class="visible-lg">Key</th>
                    <th class="hidden-xs">Rank</th>
                    <th>Scientific Name</th>
                    <th class="hidden-xs hidden-sm">Vernacular  Names</th>
                    <th class="hidden-xs hidden-sm">Canonical Name</th>
                    <th class="visible-lg">According to</th>
                    <th class="hidden-xs">Status</th>
                    <th class="hidden-xs">Name Type</th>
                    <th class="hidden-xs"># Occur.</th>
                    <th class="hidden-xs"># Desc.</th>
                    <th class="visible-lg">Author</th>
                    <th class="visible-lg">Published in</th>
                    <!--
                                                canonicalName
                                                accordingTo
                                                extinct
                                                numDescendants
                                                numOccurrences
                                                publishedIn
                                                synonym
                    -->
                </tr>
                </thead>
                <tbody><tr class="fancytree-active fancytree-expanded fancytree-exp-n fancytree-ico-e">
                    <td class="visible-lg">8347237</td><td class="hidden-xs">GENUS</td><td><span class="fancytree-node fancytree-level-1" style="padding-left: 0px;"><span class="fancytree-title">Asperula Gled.</span></span></td><td class="hidden-xs hidden-sm">
                        <div class="truncate" title=""></div></td><td class="hidden-xs hidden-sm">Asperula</td><td class="visible-lg">
                        <div class="truncate"></div></td><td class="hidden-xs">DOUBTFUL</td><td class="hidden-xs">SCIENTIFIC</td><td class="hidden-xs">0</td><td class="hidden-xs">0</td><td class="visible-lg">
                        <div class="truncate" title="Gled.">Gled.</div></td><td class="visible-lg">
                        <div class="truncate"></div></td></tr><tr class="fancytree-expanded fancytree-exp-n fancytree-ico-e">
                    <td class="visible-lg">7911309</td><td class="hidden-xs">GENUS</td><td><span class="fancytree-node fancytree-level-1" style="padding-left: 0px;"><span class="fancytree-title">Asperulus Schaeffer, 1761</span></span></td><td class="hidden-xs hidden-sm">
                        <div class="truncate" title=""></div></td><td class="hidden-xs hidden-sm">Asperulus</td><td class="visible-lg">
                        <div class="truncate"></div></td><td class="hidden-xs">SYNONYM</td><td class="hidden-xs">SCIENTIFIC</td><td class="hidden-xs">0</td><td class="hidden-xs">0</td><td class="visible-lg">
                        <div class="truncate" title="Schaeffer, 1761">Schaeffer, 1761</div></td><td class="visible-lg">
                        <div class="truncate"></div></td></tr><tr class="fancytree-expanded fancytree-exp-n fancytree-ico-e">
                    <td class="visible-lg">7901414</td><td class="hidden-xs">GENUS</td><td><span class="fancytree-node fancytree-level-1" style="padding-left: 0px;"><span class="fancytree-title">Asperulus Klein, 1776</span></span></td><td class="hidden-xs hidden-sm">
                        <div class="truncate" title=""></div></td><td class="hidden-xs hidden-sm">Asperulus</td><td class="visible-lg">
                        <div class="truncate"></div></td><td class="hidden-xs">SYNONYM</td><td class="hidden-xs">SCIENTIFIC</td><td class="hidden-xs">0</td><td class="hidden-xs">0</td><td class="visible-lg">
                        <div class="truncate" title="Klein, 1776">Klein, 1776</div></td><td class="visible-lg">
                        <div class="truncate"></div></td></tr><tr class="fancytree-expanded fancytree-exp-n fancytree-ico-e">
                    <td class="visible-lg">2916597</td><td class="hidden-xs">GENUS</td><td><span class="fancytree-node fancytree-level-1" style="padding-left: 0px;"><span class="fancytree-title">Asperula L.</span></span></td><td class="hidden-xs hidden-sm">
                        <div class="truncate" title="woodruff, Meier, fargemyskeslekta, fargemyskeslekta, färgmåror, woodruffs, Meier">woodruff, Meier, fargemyskeslekta, fargemyskeslekta, färgmåror, woodruffs, Meier</div></td><td class="hidden-xs hidden-sm">Asperula</td><td class="visible-lg">
                        <div class="truncate"></div></td><td class="hidden-xs">ACCEPTED</td><td class="hidden-xs">SCIENTIFIC</td><td class="hidden-xs">0</td><td class="hidden-xs">322</td><td class="visible-lg">
                        <div class="truncate" title="L.">L.</div></td><td class="visible-lg">
                        <div class="truncate"></div></td></tr><tr class="fancytree-expanded fancytree-exp-n fancytree-ico-e">
                    <td class="visible-lg">8424786</td><td class="hidden-xs">GENUS</td><td><span class="fancytree-node fancytree-level-1" style="padding-left: 0px;"><span class="fancytree-title">Asperulus Walbaum, 1792</span></span></td><td class="hidden-xs hidden-sm">
                        <div class="truncate" title=""></div></td><td class="hidden-xs hidden-sm">Asperulus</td><td class="visible-lg">
                        <div class="truncate"></div></td><td class="hidden-xs">SYNONYM</td><td class="hidden-xs">SCIENTIFIC</td><td class="hidden-xs">0</td><td class="hidden-xs">0</td><td class="visible-lg">
                        <div class="truncate" title="Walbaum, 1792">Walbaum, 1792</div></td><td class="visible-lg">
                        <div class="truncate"></div></td></tr><tr class="fancytree-expanded fancytree-lastsib fancytree-statusnode-paging fancytree-exp-nl fancytree-ico-e">
                    <td class="visible-lg">
                    </td><td class="hidden-xs">
                    </td><td><span class="fancytree-node fancytree-level-1" style="padding-left: 0px;"><span class="fancytree-title">(1383 more)</span></span></td><td class="hidden-xs hidden-sm">
                    </td><td class="hidden-xs hidden-sm">
                    </td><td class="visible-lg">
                    </td><td class="hidden-xs">
                    </td><td class="hidden-xs">
                    </td><td class="hidden-xs">
                    </td><td class="hidden-xs">
                    </td><td class="visible-lg">
                    </td><td class="visible-lg">
                    </td></tr></tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="btn-group">
            <button id="btnPin" class="btn btn-default btn-xs"><i class="fa fa-window-minimize" aria-hidden="true"></i></button>
            <button id="btnUnpin" class="btn btn-default btn-xs" disabled="disabled"><i class="fa fa-window-restore" aria-hidden="true"></i></button>
        </div>
        <label><input type="checkbox" id="autocollapse" >autocollapse</label>

        <div id="tree" class="fancytree-fade-expander">

        </div>
    </div>
    <div class="col-md-6">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="active">Элемент не выбран</li>
        </ol>
        <span id="tmplInfoPane"></span>
    </div>
</div>
<!--div class="panel panel-warning">
    <div class="panel-heading">
        Disclaimer
    </div>
    <div class="panel-body">
        <p>
            This site accesses data from external sources, namely the
            <a href="http://www.gbif.org/">Global Biodiversity Information Facility (GBIF)</a> database.
            There is no guarantee, that the display is correct, complete, or
            permanently available. Please refer to those original sources for
            authorative information.
        </p>
        <p>
            Copyright © 2015 Martin Wendt. Created as a demo for
            <a href="https://github.com/mar10/fancytree">Fancytree</a>.
        </p>
    </div>
</div-->
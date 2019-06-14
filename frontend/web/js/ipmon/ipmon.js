$(document).ready(function (e) {
    var timerMap = {};
    function _delay(tag, ms, callback) {
        /*jshint -W040:true */
        var that = this;

        tag = "" + (tag || "default");
        if( timerMap[tag] != null ) {
            clearTimeout(timerMap[tag]);
            delete timerMap[tag];
            // console.log("Cancel timer '" + tag + "'");
        }
        if( ms == null || callback == null ) {
            return;
        }
        // console.log("Start timer '" + tag + "'");
        timerMap[tag] = setTimeout(function(){
            // console.log("Execute timer '" + tag + "'");
            callback.call(that);
        }, +ms);
    }



    function updateControls() {
        var query = $.trim($("input[name=query]").val());
        $("#btnPin")
            .attr("disabled", !tree.getActiveNode());
        $("#btnUnpin")
            .attr("disabled", !tree.isFilterActive())
            .toggleClass("btn-success", tree.isFilterActive());
        $("#btnResetSearch")
            .attr("disabled", query.length === 0);
        $("#btnSearch")
            .attr("disabled", query.length < 2);
    }
    function updateItemDetails(key) {
        $("#tmplDetails").addClass("busy");

        $.ajax({
            type: "get",
            url: "/ipmon/ajax/root?id=" + key,
            // data:{
            //       'callerid':caller,
            //       'new_peer':newcaller,
            //       'trash_id':history
            //   },
            //dataType: "script",
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function(result){
                var jsonData = JSON.parse(result);
                $.get(
                    '/ipmon/detail',
                    {
                        data:jsonData,
                    },
                    function(e){
                        $('#tmplInfoPane').html(e)
                    }
                );

                console.log(jsonData);
                // $("#address").text(jsonData[0].fias_text);
                // $("#vendor").text(jsonData[0].vendor);
                // $("#model").text(jsonData[0].model);
                // $("#comment").text(jsonData[0].comment);
            }
        });




        // console.log(tree.getActiveNode());

        /*
         $.when(
         // _callWebservice("species/" + key),
         // _callWebservice("species/" + key + "/speciesProfiles"),
         // _callWebservice("species/" + key + "/synonyms"),
         // _callWebservice("species/" + key + "/descriptions"),
         // _callWebservice("species/" + key + "/media")

         ).done(function(species, profiles, synonyms, descriptions, media){
         // Requests are resolved as: [ data, statusText, jqXHR ]
         species = species[0];
         profiles = profiles[0];
         synonyms = synonyms[0];
         descriptions = descriptions[0];
         media = media[0];

         var info = $.extend(species, {
         profileList: profiles.results, // marine, extinct
         profile: profiles.results.length === 1 ? profiles.results[0] : null, // marine, extinct
         synonyms: synonyms.results,
         descriptions: descriptions.results,
         descriptionsByLang: {},
         media: media.results,
         now: new Date().toString()
         });

         $.each(info.descriptions, function(i, o){
         if( !info.descriptionsByLang[o.language] ) {
         info.descriptionsByLang[o.language] = [];
         }
         info.descriptionsByLang[o.language].push(o);
         });

         console.log("updateItemDetails", info);
         $("#tmplDetails")
         // .html(tmplDetails(info))
         .removeClass("busy");
         $("#tmplMedia")
         // .html(tmplMedia(info))
         .removeClass("busy");
         $("#tmplInfoPane")
         .html(tmplInfoPane(info))
         .removeClass("busy");

         $("[data-toggle='popover']").popover();
         $(".carousel").carousel();
         $("#mediaCounter").text("" + (media.results.length || ""));
         // $("[data-toggle='collapse']").collapse();
         updateControls();
         });*/
    }
// don't trigger now, since we need the the taxonTree root nodes to be loaded first

    glyphOpts = {
        map: {
            doc: "glyphicon glyphicon-file",
            docOpen: "glyphicon glyphicon-file",
            checkbox: "glyphicon glyphicon-unchecked",
            checkboxSelected: "glyphicon glyphicon-check",
            checkboxUnknown: "glyphicon glyphicon-share",
            dragHelper: "glyphicon glyphicon-play",
            dropMarker: "glyphicon glyphicon-arrow-right",
            error: "glyphicon glyphicon-warning-sign",
            expanderClosed: "glyphicon glyphicon-menu-right",
            expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
            expanderOpen: "glyphicon glyphicon-menu-down",  // glyphicon-collapse-down
            folder: "fa fa-folder",
            folderOpen: "glyphicon glyphicon-folder-open",
            loading: "glyphicon glyphicon-refresh glyphicon-spin"
        }
    };

  /*  $("#tree").fancytree({
        extensions: ["filter", "glyph", "wide"],
        filter: {
            mode: "hide"
        },
        glyph: glyphOpts,
        autoCollapse: true,
        activeVisible: true,
        autoScroll: true,
        source: {
            // url: "/ipmon/ajax/root",
            debugDelay: 1
        },
        init: function(event, data) {
            updateControls();
            $(window).trigger("hashchange"); // trigger on initial page load
        },
        lazyLoad: function(event, data) {
            // console.log(data);
            var node = data.node;
            // Issue an ajax request to load child nodes
            data.result = {
                // url: "/ipmon/ajax/child",
                type:"post",
                data: {
                    key: node.key
                },
                // success:function (eve) {
                //     console.log(eve);
                // }
            }
        },
        /*
         postProcess: function(event, data) {
         var response = data.response;

         data.node.info("taxonTree postProcess", response);
         data.result = $.map(response.results, function(o){
         return o && {title: o.vernacularName || o.canonicalName, key: o.key, comment: o.comment, folder: true, lazy: true};
         });
         if( response.endOfRecords === false ) {
         data.result.push({
         title: "(more)",
         statusNodeType: "paging"
         });
         } else {
         delete data.node.lastSourceOpts;
         }
         },*/
  /*
        toggleEffect: {
            effect: "drop",
            options: {direction: "right"}, duration: 150 },
        wide: {
            iconWidth: "1em",     // Adjust this if @fancy-icon-width != "16px"
            iconSpacing: "0.5em", // Adjust this if @fancy-icon-spacing != "3px"
            levelOfs: "1.5em"     // Adjust this if ul padding != "16px"
        },
        activate: function(event, data) {
            $("#tmplDetails").addClass("busy");
            $("ol.breadcrumb").addClass("busy");
            updateControls();
            // console.log(data.node);
            _delay("showDetails", 500, function(){
                updateItemDetails(data.node.key);
                updateBreadcrumb(data.node.key);
            });
        },
        /*activate: function(event, data){
         // A node was activated: display its title:
         console.log(data);
         var node = data.node;
         // console.log(data.node);
         //  $("#vendor").text(node.vendor);
         //      $("#model").text(node.vendor_model);
         //      $("#address").text(node.fias_text);
         //      $("#ip_address").text(node.ip);
         $.ajax({
         type: "get",
         url: "/ipmon/ajax/root?id=" + node.key,
         // data:{
         //       'callerid':caller,
         //       'new_peer':newcaller,
         //       'trash_id':history
         //   },
         //dataType: "script",
         error: function (XMLHttpRequest, textStatus, errorThrown) {
         console.log(textStatus);
         },
         success: function(result){
         var jsonData = JSON.parse(result);
         console.log(jsonData);
         $("#address").text(jsonData[0].fias_text);
         $("#vendor").text(jsonData[0].vendor);
         $("#model").text(jsonData[0].model);
         $("#comment").text(jsonData[0].comment);
         }
         });
         console.log(node)
         },*/
        /*clickPaging: function(event, data) {
         // Load the next page of results
         var source = $.extend(true, {}, data.node.parent.lastSourceOpts);
         source.data.offset = data.node.parent.countChildren() - 1;
         data.node.replaceWith(source);
         }*/
    });
/*
    var tree = $("#tree").fancytree("getTree");*/

    function updateBreadcrumb(key, loadTreeNodes) {
        var ol = $("ol.breadcrumb").addClass("busy"),
            activeNode = tree.getActiveNode();

        if( activeNode && activeNode.key !== key ) {
            activeNode.setActive(false); // deactivate, in case the new key is not found
        }
        var parents=[];
        console.log(activeNode);

        if(activeNode.parent !== undefined){
            // console.log("Parent exist");
            var parent = activeNode.parent;
            // console.log(parent);
            var i=0;
            while(parent.parent!=undefined){
                parents[i] = {
                    "title":parent.data.name,
                    "key":parent.key
                };
                parent = parent.parent;
                i++;
            }
        }else{
            console.log("This is a root");
        }
        // console.log(parents);
        // $.when(
        //     activeNode.parent,
        //     activeNode.data.key
        //     _callWebservice("species/" + key + "/parents"),
        //     _callWebservice("species/" + key)
        // ).done(function(parents, node)
        {
            // Both requests resolved (result format: [ data, statusText, jqXHR ])
            var nodeList = parents,
                keyList = [];

            nodeList.unshift({
                "title":activeNode.data.name,
                "key":activeNode.key
            });


            // console.log(nodeList);
            // Display as <OL> list (for Bootstrap breadcrumbs)
            ol.empty().removeClass("busy");
            $.each(nodeList.reverse(), function(i, o){
                // var name = o.vernacularName || o.canonicalName;
                var name = o.title;
                keyList.push(o.key);
                if( "" + o.key === "" + key ) {
                    ol.append(
                        $("<li class='active'>").append(
                            $("<span>", {
                                text: name,
                                // title: o.rank
                            })));
                } else {
                    ol.append(
                        $("<li>").append(
                            $("<a>", {
                                href: "#key=" + o.key,
                                text: name,
                                // title: o.rank
                            })));
                }
            });
            if( loadTreeNodes ) {
                // console.log("updateBreadcrumb - loadKeyPath", keyList);
                tree.loadKeyPath("/" + keyList.join("/"), function(node, status){
                    // console.log("... updateBreadcrumb - loadKeyPath " + node.title + ": " + status);
                    console.log("current node -----", status);
                    switch( status ) {
                        case "loaded":
                            node.makeVisible();
                            break;
                        case "ok":
                            node.setActive();
                            // node.makeVisible();
                            break;
                    }
                });
            }
        }
        // );
    }


 

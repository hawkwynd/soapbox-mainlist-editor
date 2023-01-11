// handler for the top button positioning 
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

$(function() {

    console.log('mainlist.js is loaded.')

    // modal close listener
    $('#infoModal').on('hidden.bs.modal', function() {
        // do something…
        $('.delete').show()
        $('.uBtn').show()
        $('.notify').removeClass('text-danger')
    })


    // listen for checkbox click
    $('input:checkbox').change(
        function(){

            $('input:checkbox').not( this ).prop('checked', false);
            
            
        });


    // listInfo();
    var info = listInfo()


    listDisplay(info.count + ' tracks, last updated ' + info.lastModifiedago);
    //Get the button:
    mybutton = document.getElementById("myBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() { scrollFunction() };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {

            var header = document.getElementById('header');
            var sticky = header.offsetTop;

            if (window.pageYOffset > sticky) {
                $('#header').css({ top: '' })
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }

            mybutton.style.display = "block";

        } else {
            mybutton.style.display = "none";
            $('#header').css({ top: '180px' })
        }
    }

    renderGenreSelect();

    function renderGenreSelect(){
        $.ajax({
            url: 'jarvis.php',
            type: 'POST',
            dataType: 'json',
            data: {'action':'getGenres'},
            success: function( result ){
            
                var options = '<option>Select A Genre</option>';

                $.each(result, function(index, genre ){
                    // console.log( genre )
                    options += '<option value="' + genre + '">'+genre+'</option>'

                })

                $('.cols-5').append('<div><select class="form-control" name="genre">' + options + '</select></div>')
            }
        })
    }

    $('.btn-in_list').on('click', function(){
   
        let inMainList = null;

        $.ajax({
            url: 'jarvis.php', // url where to submit the request
            type: "POST", // type of action POST || GET
            dataType: 'json', // data type
            data: {'action':'list'},
            success: function( result ) {
                
                inMainList = result;
                selected = 0; 

                // render listMain 
                $("#results").empty()
        
                        var info = listInfo()
                        
                        listDisplay(info.count + ' tracks, last updated ' + info.lastModifiedago);
                        
                        $("#results").html(
                            '<div  class="row text-white bg-primary align-items-start py-3" id="header">' +
                            '<div class="col">Title</div>' +
                            '<div class="col">Artist</div>' +
                            '<div class="col">Album</div>' +
                            '<div class="col">Track</div>' +
                            '<div class="col">Year</div>' +
                            '<div class="col">Format</div>' +
                            '<div class="col">Genre</div>' +
                            // '<div class="col actionCol">Actions</div>' +
                            '</div>'
                        )
        
                        $.each( inMainList, function( index, val ) {
        
                            // console.log( val );
        
                            row = val.fileLine == null ? $('<div class="row border bg-light align-items-start py-2 line-item"></div>') : $('<div class="row border bg-light align-items-start py-2 selected line-item"></div>');
        
                            // create row of data
                            el = $(
                                '<div class="col">' + val.title + '</div>' +
                                '<div class="col">' + val.artist + '</div>' +
                                '<div class="col" >' + val.album + '</div>' +
                                '<div class="col">' + val.track + '</div>' +
                                '<div class="col">' + val.year + '</div>' +
                                '<div class="col">' + val.fileformat + '</div>' +
                                '<div class="col">' + val.genre + '</div>'
                            )
        
        
                            // if (val.fileLine !== null) {
                            //     selected++;
                            //     out = $('<div class="col actionCol selected"><button title="Delete From Main" class="btn btn-danger delBtn fas fa-trash-alt" id="' + val.fileLine + '"</button>' +
                            //         ' <button title="Edit Metadata" class="btn btn-warning eBtn fas fa-edit" path id="' + val.filenamepath + '"></button>' +
                            //         '<button title="View Metadata" class="btn btn-info fa fa-eye vBtn" data-bs-toggle="modal" data-bs-target="#infoModal" id="' + val.filenamepath + '"></button>' +
                            //         '</div>');
        
                            // } else {
                            //     out = $('<div class="col actionCol"><button title="Add To Main" class="btn btn-success mainBtn fas fa-plus" id="' + val.filenamepath + '"></button>' +
                            //         ' <button title="Edit Metadata" class="btn btn-warning eBtn fas fa-edit" path id="' + val.filenamepath + '"></button>' +
                            //         '<button title="View Metadata" class="btn btn-info fa fa-eye vBtn" data-bs-toggle="modal" data-bs-target="#infoModal" id="' + val.filenamepath + '"></button>' +
                            //         '</div>');
                            // }
        
                            row.append(el);
                            
                            $("#results").append(row);
        
                            // row.append(out);
        
                        });
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text);
            }
            
        })
  

    })



    // Search form listener 
    $("form").submit( function(event) {

        var titleTxt = $('#title').val();

        $.ajax({
            url: 'jarvis.php', // url where to submit the request
            type: "POST", // type of action POST || GET
            dataType: 'json', // data type
            data: $("form").serialize(), // post data || get data
            success: function( result ) {

                // console.log(result);

                $("#results").empty()

                var info = listInfo()
                
                listDisplay(info.count + ' tracks, last updated ' + info.lastModifiedago);
                
                $("#results").html(
                    '<div  class="row text-white bg-dark align-items-start py-3" id="header">' +
                    '<div class="col">Title</div>' +
                    '<div class="col">Artist</div>' +
                    '<div class="col">Album</div>' +
                    '<div class="col">Track</div>' +
                    '<div class="col">Year</div>' +
                    '<div class="col">Format</div>' +
                    '<div class="col">Genre</div>' +
                    '<div class="col actionCol">Actions</div>' +
                    '</div>'
                )
                var selected = 0;

                $.each(result, function( index, val ) {

                    // console.log( val );
                    row = val.fileLine == null ? $('<div class="row border bg-light align-items-start py-2 line-item"></div>') : $('<div class="row border bg-light align-items-start py-2 selected line-item"></div>');

                    // create row of data
                    el = $(
                        '<div class="col">' + val.title + '</div>' +
                        '<div class="col">' + val.artist + '</div>' +
                        '<div class="col" >' + val.album + '</div>' +
                        '<div class="col">' + val.track + '</div>' +
                        '<div class="col">' + val.year + '</div>' +
                        '<div class="col">' + val.fileformat + '</div>' +
                        '<div class="col">' + val.genre + '</div>'
                    )


                    if (val.fileLine !== null) {
                        selected++;
                        out = $('<div class="col actionCol selected"><button title="Delete From Main" class="btn btn-danger delBtn fas fa-trash-alt" id="' + val.fileLine + '"</button>' +
                            ' <button title="Edit Metadata" class="btn btn-warning eBtn fas fa-edit" path id="' + val.filenamepath + '"></button>' +
                            '<button title="View Metadata" class="btn btn-info fa fa-eye vBtn" data-bs-toggle="modal" data-bs-target="#infoModal" id="' + val.filenamepath + '"></button>' +
                            '</div>');

                    } else {
                        out = $('<div class="col actionCol"><button title="Add To Main" class="btn btn-success mainBtn fas fa-plus" id="' + val.filenamepath + '"></button>' +
                            ' <button title="Edit Metadata" class="btn btn-warning eBtn fas fa-edit" path id="' + val.filenamepath + '"></button>' +
                            '<button title="View Metadata" class="btn btn-info fa fa-eye vBtn" data-bs-toggle="modal" data-bs-target="#infoModal" id="' + val.filenamepath + '"></button>' +
                            '</div>');
                    }

                    row.append(el);
                    
                    $("#results").append(row);

                    row.append(out);

                });
                

                // Listen for view metadata buttons
                $('.vBtn').on('click', function() {

                    var result = getID3(this.id);
                    $('.modal-body').empty();

                    $.each(result, function(key, value) {
                        $('.modal-body').append('<div class="mb-2">' + key + '<span class="' + key + ' ml-2 mb-2">' + value + '</span></div>');
                    });

                    // render title modal
                    $('.modal-title').text(result.Artist + ' - ' + result.Title);
                    $('.notify').empty();

                    $('#infoModal').modal('show');

                });

                // Listen to Edit buttons 
                $('.eBtn').on('click', function() {

                    var eUrl = "http://soapbox/requester/php-getid3/demos/demo.write.php?Filename=";
                    var params = encodeURIComponent(this.id)

                    // v2 will do this in a modal form
                    window.open(eUrl + params, '_blank')

                });

                

                // Listen to Add to Main buttons
                $('.mainBtn').on('click', function() {

                    result = addToMainList(this.id)

                    $(this).text(result.status).attr('disabled', 'disabled');

                    var info = listInfo()

                    listDisplay(info.count + ' tracks, last updated ' + info.lastModifiedago);

                    $('.toast-header').removeClass('bg-danger').addClass('text-white bg-success');
                    $('.toast-header').html(
                        '<i class="fa fa-thumbs-up"></i> <strong class="mr-auto">' + result.message + '</strong>' +
                        '<small>' + info.filename + '</small>' +
                        '<button type="button" class="ml-3 mb-1 close" data-dismiss="toast">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>'
                    );

                    $('.toast-body').text('File: ' + result.filenampath);
                    $('.toast-body').append('<div>' + info.count + ' total tracks in ' + info.filename + '</div>')

                    // show the toast, for 5000ms 
                    $('.toast').css('max-width', '800px').toast({ delay: 3000 }).toast('show');

                    // trigger GO button for list refresh
                    $('#goBtn').click();

                });

                // Listen to Delete From Main buttons
                $('.delBtn').on('click', function() {

                    result = deleteFromMain(this.id)

                    $(this).text(result.status).attr('disabled', 'disabled')

                    var info = listInfo()

                    listDisplay(info.count + ' tracks, last updated ' + info.lastModifiedago);

                    $('.toast-header').removeClass('bg-success').addClass('text-white bg-danger');

                    $('.toast-header').html(
                        '<i class="fas fa-trash"></i> <strong class="mr-auto">' + result.filename + ' deleted</strong>' +
                        '<small>' + info.filename + '</small>' +
                        '<button type="button" class="ml-3 mb-1 close" data-dismiss="toast">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>'
                    );

                    $('.toast-body').text('File: ' + result.message);
                    $('.toast-body').append('<div>' + info.count + ' total tracks in ' + info.filename + '</div>')

                    // show the toast, for 5000ms 
                    $('.toast').css('max-width', '800px').toast({ delay: 3000 }).toast('show');

                    // trigger GO button
                    $('#goBtn').click();

                });

                
                if (selected > 0) $('.listinfo').append('<span>[' + selected + ' loaded in main] <button class="btn btn-sm btn-primary" id="showmainlist">Show only mainlist</button></span>')


                // Showmainlist button listener 
                $('#showmainlist').on('click', function(){                   
                    $(this).attr('id','showall').html('Main list files only').attr('disabled', 'disabled');
                    $('.line-item').not('.selected').hide();
                    // $('#results').css('margin-top': '60px');

                })

            },
            error: function(xhr, resp, text) {
                console.log( xhr.responseText );
            }
        })

        event.preventDefault();
    });


   
    // Delete Button Listner
    $('.delete').on('click', function() {
        var filenamepath = $('.filenamepath').text();

        if (confirm('Do you really want to delete ' + filenamepath + '? \r\n There is no Un-DO for this.')) {

            var result = deleteFromKeep( filenamepath )
            
            if(result.status == 'OK'){
                $('.notify').text('Deleted FOREVER!').addClass('text-danger');
                $('.uBtn').hide();
                $('.delete').hide();
                $('.filenamepath').text('Deleted FOREVER').addClass('text-danger');
                $('.Filename').text('Deleted FOREVER').addClass('text-danger');
                // trigger GO button to refresh listing
                $('#goBtn').click();
            } else{
                $('.notify').text('Uh oh.. something bad just happened.').addClass('text-danger')
            }

        }
    })

    /**
     * 
     * @param {*} filenamepath 
     * @returns rowcount
     * Delete from library and unset the file from the directory
     * 
     */

    function deleteFromKeep(filenamepath) {

        $.ajax({
            url: "jarvis.php",
            type: "POST",
            async: false,
            data: {
                'action': 'kill',
                'filenamepath': filenamepath
            },
            dataType: "json",
            success: function(result) {
                returnValue = result
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text)
            }

        })

        return returnValue;
    }


    // UPDATE DB Button listener

    $('.uBtn').on('click', function() {

        var myData = {};

        var artist = $('.Artist').text();
        var title = $('.Title').text();
        var album = $('.Album').text();
        var genre = $('.Genre').text();
        var year = $('.Year').text();
        var filenamepath = $('.filenamepath').text();
        var filename = $('.Filename').text();
        var fileformat = $('.fileformat').text();
        var track = $('.Track').text();

        // build array of data to post
        myData = { artist: artist, title: title, album: album, year: year, genre: genre, filenamepath: filenamepath, filename: filename, fileformat: fileformat ,track: track };
        
        var paramJSON = JSON.stringify(myData);

        // console.log( "sending myData")
        // console.log( paramJSON )

        result = updateDB(paramJSON);

        var notify = result == 1 ? 'Update Successful' : 'No Changes made';

        $('.notify').text(notify).addClass('text-success mr-3 px-2');

        // Refresh listing
        $('#goBtn').click();

    });

    // update db with new data 
    function updateDB(payload) {

        let returnValue = null;

        $.ajax({
            url: "jarvis.php",
            type: "POST",
            async: false,
            data: {
                'action': 'updateKeep',
                'payload': payload
            },
            dataType: "json",
            success: function(result) {
                returnValue = result
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text)
            }

        })

        return returnValue;
    }


    // Delete From Main playlist
    function deleteFromMain(lineno) {
        let returnValue = null;

        $.ajax({
            url: 'jarvis.php',
            type: "POST",
            dataType: 'json',
            async: false,
            data: {
                'action': 'deleteFromMain',
                'lineno': lineno,
            },
            success: function(result) {
                returnValue = result
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });

        return returnValue;
    }


    // GetID3 array of filename
    function getID3(filename) {
        // console.log( 'getID3 called...')
        let returnValue = null;
        $.ajax({
            url: "jarvis.php",
            type: "POST",
            dataType: "json",
            async: false,
            data: {
                'action': 'getID3',
                'filename': filename,
            },
            success: function(result) {
                returnValue = result
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });

        return returnValue;
    }



    // Add to main playlist
    function addToMainList( item ) {
        let returnValue = null;

        $.ajax({
            url: 'jarvis.php', // url where to submit the request
            type: "POST", // type of action POST || GET
            dataType: 'json', // data type
            async: false,
            data: {
                'action': 'addToMain',
                'filenampath': item,

            }, // post data || get data
            success: function(result) {

                returnValue = result

            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });

        return returnValue;
    }


    function listDisplay(info) {
       
        $('.listinfo').html('<span>' +  info + '</span>');
       
    }



    // ask how many in list
    function listInfo() {

        let returnValue = null;

        $.ajax({
            url: "jarvis.php",
            type: "POST",
            dataType: "json",
            async: false,
            data: { 'action': 'count', 'list': 'main' },
            success: function(result) {
                returnValue = result;
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        })

        return returnValue
    }

    function listMain(){
        
        let mainListData = null;

        $.ajax({
            url: 'jarvis.php', // url where to submit the request
            type: "POST", // type of action POST || GET
            dataType: 'json', // data type
            data: {'action':'list'},
            success: function( result ) {
                
                mainListData=result;
            },
            error: function(xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        })
        
        return mainListData

    }
    

});
(function($) {
  var themovieddUrl = "https://api.themoviedb.org/3/";

  var postersList;
  $("#search-imdb-tv").on("click",function() {
    var value = $("#input-imdb").val();
    var type = $("#type-imdb").val();
    if (type == "title") {

      var d = {
        "api_key": themoviedb_key,
        "query": value,
        "language": language
      };
      $.ajax({
        data: d,
        url: themovieddUrl + "search/tv",
        success: function(result) {
          if (result.total_results == 0) {
            alert("No Movies Founded");
          } else {
            var List = "";
            postersList = new Array();
            for (var i = 0; i < result.results.length; i++) {
              postersList[i] = result.results[i];
              List += "<div id='" + i + "'  alt=" + result.results[i].id + " class='poster-search select_serie_tv'><img src='https://image.tmdb.org/t/p/w400" + result.results[i].poster_path + "'/><span>" + result.results[i].original_name + "</span><div>" + result.results[i].first_air_date + "</div></div>";
            }
            $("#result_search").html(List);
            $("#div1").show();
          }
        }
      });
    } else {
      var d = {
        "api_key": themoviedb_key,
        "language": language,
        "external_source": "imdb_id"
      };
      $.ajax({
        data: d,
        url: themovieddUrl + "find/" + value,
        success: function(result) {
          if (result.tv_results.length == 0) {
            alert("No Movies Founded");
          } else {
            var List = "";
            postersList = new Array();
            for (var i = 0; i < result.tv_results.length; i++) {
              postersList[i] = result.tv_results[i];
              List += "<div  id='" + i + "'  alt=" + result.tv_results[i].id + " class='poster-search select_serie_tv'><img src='https://image.tmdb.org/t/p/w400" + result.tv_results[i].poster_path + "'/><span>" + result.tv_results[i].original_name + "</span><div>" + result.tv_results[i].first_air_date + "</div></div>";
            }
            $("#result_search").html(List);
            $("#div1").show();
          }
        }
      });
    }
  });
  $(document).on("click", ".select_serie_tv", function() {
    SelectSerieTv($(this).attr("id"));
  });

  function SelectSerieTv(index) {
    $(".poster-infos").show();
    $("#title-poster").html(postersList[index].original_name);
    $("#date-poster").html(postersList[index].first_air_date);
    $("#overview-poster").html(postersList[index].overview);
    $("#rating-poster").html(postersList[index].vote_average);

    $("#image-poster").attr("src", "https://image.tmdb.org/t/p/w500" + postersList[index].poster_path);
    $("#div1").hide();
    $("#result_search").html("");
    $("#form_id").val(postersList[index].id);

  }
  $("#import_movie").on("click",function() {
    $(this).html("<span class='material-icons animation_icon'>motion_photos_on</span> Importing...");
  });
  $("#search-imdb").on("click",function() {
    var value = $("#input-imdb").val();
    var type = $("#type-imdb").val();
    if (type == "title") {
      var d = {
        "api_key": themoviedb_key,
        "query": value,
        "language": language
      };
      $.ajax({
        data: d,
        url: themovieddUrl + "search/movie",
        success: function(result) {
          if (result.total_results == 0) {
            alert("No Movies Founded");
          } else {
            var List = "";
            postersList = new Array();
            for (var i = 0; i < result.results.length; i++) {
              postersList[i] = result.results[i];
              List += "<div  id='" + i + "' alt=" + result.results[i].id + " class='poster-search select_serie_movie'><img src='https://image.tmdb.org/t/p/w400" + result.results[i].poster_path + "'/><span>" + result.results[i].original_title + "</span><div>" + result.results[i].release_date + "</div></div>";
            }
            $("#result_search").html(List);
            $("#div1").show();
          }
        }
      });
    } else {
      var d = {
        "api_key": themoviedb_key,
        "language": language,
        "external_source": "imdb_id"
      };
      $.ajax({
        data: d,
        url: themovieddUrl + "find/" + value,
        success: function(result) {
          if (result.movie_results.length == 0) {
            alert("No Movies Founded");
          } else {
            var List = "";
            postersList = new Array();
            for (var i = 0; i < result.movie_results.length; i++) {
              postersList[i] = result.movie_results[i];
              List += "<div  id='" + i + "' alt=" + result.movie_results[i].id + " class='poster-search select_serie_movie'><img src='https://image.tmdb.org/t/p/w400" + result.movie_results[i].poster_path + "'/><span>" + result.movie_results[i].original_title + "</span><div>" + result.movie_results[i].release_date + "</div></div>";
            }
            $("#result_search").html(List);
            $("#div1").show();
          }
        }
      });
    }
  });
  $(document).on("click", ".select_serie_movie", function() {
    SelectMovie($(this).attr("id"));
  });

  function SelectMovie(index) {
    $(".poster-infos").show();
    $("#title-poster").html(postersList[index].original_title);
    $("#date-poster").html(postersList[index].release_date);
    $("#overview-poster").html(postersList[index].overview);
    $("#rating-poster").html(postersList[index].vote_average);

    $("#image-poster").attr("src", "https://image.tmdb.org/t/p/w500" + postersList[index].poster_path);
    $("#div1").hide();
    $("#result_search").html("");
    $("#form_id").val(postersList[index].id);

  }


  var apiKey = "e3dd511a";
  var apiurl = "https://www.omdbapi.com/";

  function EpisodeFunction(id) {
    var d = {
      "i": id,
      "apikey": apiKey
    };
    $.ajax({
      data: d,
      url: apiurl,
      success: function(result) {
        $("#episode_title").val(result.Title);
        $("#episode_description").val(result.Plot);
        $("#episode_classification").val(result.Rated);
        $("#episode_duration").val(result.Runtime);
        $("#img-preview-1").attr("src", result.Poster.replace("300", "1000"));
        $("#episode_image").val(result.Poster.replace("300", "1000"));
        $("#div1").hide();
      }
    });
  }

  function SerieFunction(id) {
    var d = {
      "i": id,
      "apikey": apiKey
    };
    $.ajax({
      data: d,
      url: apiurl,
      success: function(result) {
        $("#serie_title").val(result.Title);
        $("#serie_year").val(parseInt(result.Year));
        $("#serie_description").val(result.Plot);
        $("#serie_classification").val(result.Rated);
        $("#serie_duration").val(result.totalSeasons + " Seasons");
        $("#serie_imdb").val(result.imdbRating);
        $("#serie_title").val(result.Title);
        $("#serie_tags").val(result.Genre);
        $("#img-preview").attr("src", result.Poster.replace("300", "1000"));
        $("#serie_image").val(result.Poster.replace("300", "1000"));

        var genres = result.Genre.split(", ");
        for (var i = 0; i < genres.length; i++) {
          genres[i]
        }
        $("input[name='Serie[genres][]']").each(function(index, obj) {
          console.log($(this).next().html());
          $(this).prop("checked", false);

          for (var j = 0; j < genres.length; j++) {
            console.log("*" + $(this).next().html().toUpperCase() + "-" + genres[j].toUpperCase() + "*");
            if ($(this).next().html().toUpperCase() == genres[j].toUpperCase()) {
              $(this).prop("checked", true);
            }
          }
        });
        $("#div1").hide();
      }

    });
  }

  function MovieFunction(id) {
    var d = {
      "i": id,
      "apikey": apiKey
    };
    $.ajax({
      data: d,
      url: apiurl,
      success: function(result) {
        $("#movie_title").val(result.Title);
        $("#movie_year").val(parseInt(result.Year));
        $("#movie_description").val(result.Plot);
        $("#movie_classification").val(result.Rated);
        $("#movie_duration").val(result.Runtime);
        $("#movie_imdb").val(result.imdbRating);
        $("#movie_title").val(result.Title);
        $("#movie_tags").val(result.Genre);
        $("#img-preview").attr("src", result.Poster.replace("300", "1000"));
        $("#movie_image").val(result.Poster.replace("300", "1000"));

        var genres = result.Genre.split(", ");
        for (var i = 0; i < genres.length; i++) {
          genres[i]
        }
        $("input[name='Movie[genres][]']").each(function(index, obj) {
          console.log($(this).next().html());
          $(this).prop("checked", false);

          for (var j = 0; j < genres.length; j++) {
            console.log("*" + $(this).next().html().toUpperCase() + "-" + genres[j].toUpperCase() + "*");
            if ($(this).next().html().toUpperCase() == genres[j].toUpperCase()) {
              $(this).prop("checked", true);
            }
          }
        });
        $("#div1").hide();
      }

    });
  }
  $(document).on("click",".poster-search-movie",function() {
      MovieFunction($(this).attr("alt"));
  });

  $("#search").on("click",function() {
    var d = {
      "s": $("#movie_title").val(),
      "apikey": apiKey,
      "type": "movie"
    };
    $.ajax({
      data: d,
      url: apiurl,
      success: function(result) {
        var List = "";
        for (var i = 0; i < result.Search.length; i++) {
          List += "<div   alt='" + result.Search[i].imdbID + "' class='poster-search poster-search-movie'><img src='" + result.Search[i].Poster + "'/><span>" + result.Search[i].Title + "</span><div>" + result.Search[i].Year + "</div></div>";
        }
        $("#result_search").html(List);
        $("#div1").show();
      }
    });
  })
  $("#search_episodes").on("click",function() {
    var seasonNum = $("#season_id").val().replace(/^\D+/g, '');
    var d = {
      "Season": seasonNum,
      "apikey": apiKey,
      "t": $("#serie_name").val()
    };
    $.ajax({
      data: d,
      url: apiurl,
      success: function(result) {
        var List = "";
        for (var i = 0; i < result.Episodes.length; i++) {
          List += "<div  onclick=\"EpisodeFunction('" + result.Episodes[i].imdbID + "')\" alt=" + result.Episodes[i].imdbID + " class='poster-search'>" + result.Episodes[i].Title + " (Episode : " + result.Episodes[i].Episode + ") </span><div>" + result.Episodes[i].Released + "</div></div>";
        }
        $("#result_search").html(List);
        $("#div1").show();

      }
    });
  })
  $("#search_series").on("click",function() {
    var d = {
      "s": $("#serie_title").val(),
      "apikey": apiKey,
      "type": "series"
    };
    $.ajax({
      data: d,
      url: apiurl,
      success: function(result) {
        var List = "";
        for (var i = 0; i < result.Search.length; i++) {
          List += "<div  onclick=\"SerieFunction('" + result.Search[i].imdbID + "')\" alt=" + result.Search[i].imdbID + " class='poster-search'><img src='" + result.Search[i].Poster + "'/><span>" + result.Search[i].Title + "</span><div>" + result.Search[i].Year + "</div></div>";
        }
        $("#result_search").html(List);
        $("#div1").show();

      }
    });
  })
  $("#close_search").on("click",function() {
    $("#div1").hide();
  })
  $(".btn-select").on("click",function() {
    $(".input-file").click();
  })


  $(".select-video").on("click",function() {
    $(".input-video").click();
  })
  $("#Video_color").change(function() {
    $(".textarea-quotes").css("background-color", "#" + $(this).val());
    $(".quote-view").css("background-color", "#" + $(this).val());
  })

  $(".input-video").change(function(evt) {
    var $source = $('#video_here');
    $source[0].src = URL.createObjectURL(this.files[0]);
    $source.parent()[0].load();
    $source.parent("video").css({
      "display": "block"
    })
  });
  $(".img-selector").change(function() {
    readURL(this, "#img-preview");
  });
  $(".alert-dashborad .close").on("click",function() {
    $(".alert-dashborad").fadeOut();
  })
  $("#new_season_btn").on("click",function() {
    $("#new_season_dialog").show();
    return false;

  })
  $("#new_season_btn_close").on("click",function() {
    $("#new_season_dialog").hide();
    return false;
  })
  $("#movie_sourcetype").change(function(argument) {
    if ($("#movie_sourcetype").val() == 5) {
      $("#movie_sourceurl").parent().hide();
      $("#movie_sourcefile").parent().show();
    } else {
      $("#movie_sourceurl").parent().show();
      $("#movie_sourcefile").parent().hide();
    }
  });
  $("#source_type").change(function(argument) {
    if ($("#source_type").val() == 5) {
      $("#source_url").parent().hide();
      $("#source_file").parent().show();
    } else {
      $("#source_url").parent().show();
      $("#source_file").parent().hide();
    }
  });
  $("#movie_trailertype").change(function(argument) {
    if ($("#movie_trailertype").val() == 5) {
      $("#movie_trailerurl").parent().hide();
      $("#movie_trailerfile").parent().show();
    } else {
      $("#movie_trailerurl").parent().show();
      $("#movie_trailerfile").parent().hide();
    }
  });
  $("#serie_trailertype").change(function(argument) {
    if ($("#serie_trailertype").val() == 5) {
      $("#serie_trailerurl").parent().hide();
      $("#serie_trailerfile").parent().show();
    } else {
      $("#serie_trailerurl").parent().show();
      $("#serie_trailerfile").parent().hide();
    }
  });
  $("#episode_sourcetype").change(function(argument) {
    if ($("#episode_sourcetype").val() == 5) {
      $("#episode_sourceurl").parent().hide();
      $("#episode_sourcefile").parent().show();
    } else {
      $("#episode_sourceurl").parent().show();
      $("#episode_sourcefile").parent().hide();
    }
  });
  $("#slide_type").change(function() {
    if ($("#slide_type").val() == 5) {
      $("#slide_channel").parent().hide();
      $("#slide_url").parent().hide();
      $("#slide_category").parent().hide();
      $("#slide_poster").parent().hide();
      $("#slide_genre").parent().show();
    }
    if ($("#slide_type").val() == 4) {
      $("#slide_channel").parent().hide();
      $("#slide_url").parent().hide();
      $("#slide_category").parent().hide();
      $("#slide_poster").parent().show();
      $("#slide_genre").parent().hide();
    }
    if ($("#slide_type").val() == 3) {
      $("#slide_channel").parent().show();
      $("#slide_url").parent().hide();
      $("#slide_category").parent().hide();
      $("#slide_poster").parent().hide();
      $("#slide_genre").parent().hide();
    }
    if ($("#slide_type").val() == 2) {
      $("#slide_channel").parent().hide();
      $("#slide_url").parent().hide();
      $("#slide_category").parent().show();
      $("#slide_poster").parent().hide();
      $("#slide_genre").parent().hide();
    }
    if ($("#slide_type").val() == 1) {
      $("#slide_channel").parent().hide();
      $("#slide_url").parent().show();
      $("#slide_category").parent().hide();
      $("#slide_poster").parent().hide();
      $("#slide_genre").parent().hide();
    }
  });
  $(".btn-select-1").on("click",function() {
    $(".input-file-1").click();
  })
  $(".img-selector-1").change(function() {
    readURLL(this, "#img-preview-1");
  });

  $(".input-btn-3").on("click",function() {
    $(".input-file-3").click();
  })

  function readURL(input, pr) {
    if (input.files && input.files[0]) {
      var countFiles = input.files.length;
      var imgPath = input.value;
      var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

      if (extn == "png" || extn == "jpg" || extn == "jpeg") {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(pr).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      } else {
        alert("the image file is not valid please select a valid image file : png,jpg or jpeg");
      }
    }
  }



  var imagesPreview = function(input, placeToInsertImagePreview) {

    if (input.files) {
      var filesAmount = input.files.length;

      for (i = 0; i < filesAmount; i++) {
        var reader = new FileReader();

        reader.onload = function(event) {
          $($.parseHTML('<img style="width:210px;height:auto;display:inline-block;margin:15px"  class="thumbnail thumbnail-2" />')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
        }

        reader.readAsDataURL(input.files[i]);
      }
    }

  };

  $('#product_files').on('change', function() {
    imagesPreview(this, 'div.gallery');
  });



  $(".img-thum-product").on("click",function() {
    $("#image_product_view").attr({
      "src": $(this).attr("data")
    });
    $(".img-thum-product").removeClass("img-thum-product-active");
    $(this).addClass("img-thum-product-active");
  });
  $(".button-file").change(function() {
    readURL(this);
  });

  function readURL(input) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('.image-preview').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }



  // Multiple images preview in browser
  var imagesPreview = function(input, placeToInsertImagePreview) {

    if (input.files) {
      var filesAmount = input.files.length;

      for (i = 0; i < filesAmount; i++) {
        var reader = new FileReader();

        reader.onload = function(event) {
          $($.parseHTML('<img style="width:230px;height:230px;margin:10px;display:inline-block"  class="thumbnail thumbnail-2" >')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
        }

        reader.readAsDataURL(input.files[i]);
      }
    }

  };
  $('#Wallpaper_files').on('change', function() {
    $("div.gallery").html("");
    imagesPreview(this, 'div.gallery');
  });



  $(".img-selector").change(function() {
    readURLL(this, "#img-preview");
  });

  function readURLL(input, pr) {
    if (input.files && input.files[0]) {
      var countFiles = input.files.length;
      var imgPath = input.value;
      var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

      if (extn == "png" || extn == "jpg" || extn == "jpeg" || extn == "gif") {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(pr).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      } else {
        alert("the image file is not valid please select a valid image file : png,jpg or jpeg");
      }
    }
  }
})(jQuery);
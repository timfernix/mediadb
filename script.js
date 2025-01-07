$(document).ready(function () {
    loadMediaItems();
    loadWatchlistItems();
  
    $("#search-bar").on("input", function () {
        searchMediaItems($(this).val());
    });
  
    $("#add-item-btn").on("click", function () {
        addItem();
    });
  
    $("#watchlist-btn").on("click", function () {
        checkWatchlist();
    });
  
    $("#watched-btn").on("click", function () {
        watched();
    });
  
    $("#sortAZ-btn").on("click", function () {
        sortMediaItemsAZ();
    });
  });
  
  let mediaItemsData = [];
  
  function loadMediaItems() {
    $.ajax({
        type: "GET",
        url: "load_media_items.php",
        dataType: "json",
        success: function (data) {  
            mediaItemsData = data;
            displayMediaItems(data);
            updateSearchPlaceholder(data); 
        },
        error: function (xhr, status, error) {
            console.error("Error loading media items:", error);
        }
    });
  }
  
  function loadWatchlistItems() {
    $.ajax({
        type: "GET",
        url: "load_media_items.php",
        dataType: "json",
        success: function (data) {  
            displayWatchlistItems(data);
        },
        error: function (xhr, status, error) {
            console.error("Error loading watchlist items:", error);
        }
    });
  }
  
  function updateSearchPlaceholder(data) {
    let movieCount = 0;
    let seriesCount = 0;
    let gameCount = 0;
    let countAll = 0;
  
    $.each(data, function (index, item) {
        if (item.type === "Movie") {
            movieCount++;
            countAll++;
        } else if (item.type === "Series") {
            seriesCount++;
            countAll++;
        } else if (item.type === "Game") {
            gameCount++;
            countAll++;
        }
    });
  
    $("#search-bar").attr("placeholder", `🔍 Search ${movieCount} movies, ${seriesCount} series and ${gameCount} games (${countAll} total)`);
  }
  
  function displayMediaItems(data) {
      $(".media-container").empty();
      
      let currentlyWatchingItems = [];
      let favoriteItems = [];
      let normalItems = [];
    
      $.each(data, function (index, item) {
          if (item.watchlist === "1") {
              return; 
          }
    
          const mediaItem = $('<div class="media-item">');
          if (item.favorite === "1") {
              favoriteItems.push(mediaItem);
          } else if (item.currentlyWatching === "1") {
              currentlyWatchingItems.push(mediaItem);
          } else {
              normalItems.push(mediaItem);
          }
    
          let emoji;
          if (item.type == "Movie") {
              emoji = "🎥 ";
          } else if (item.type == "Series") {
              emoji = "📺 ";
          } else if (item.type == "Game") {
              emoji = "🎮 ";
          }
    
          if (item.favorite === "1") {
              mediaItem.append('<div class="favorite">❤️ Favorite ❤️</div>');
          } else if (item.currentlyWatching === "1") {
              mediaItem.append('<div class="currently-watching">✨ Currently Watching ✨</div>');
          } else if (item.type === "Game") {
              mediaItem.append('<div class="watched">✅ Played ✅</div>');
          } else {
              mediaItem.append('<div class="watched">✅ Watched ✅</div>');
          }
    
          mediaItem.append('<img src="' + item.coverUrl + '">');
          mediaItem.append('<div class="title">' + item.title + "</div><br> <br> ");
          mediaItem.append("<p>📢 <i>''" + item.originTitle + "''</i></p>");
          mediaItem.append(emoji + "<b>" + item.type + "</b>");
          mediaItem.append("<p>🎭 " + item.genre + "</p>");
          mediaItem.append("<p>📆 " + item.releaseDate + "</p>");
    
          if (item.type === "Game") {
              mediaItem.append("<p>👨‍ 💻 " + item.director + "</p>");
          } else {
              mediaItem.append("<p>🎥 " + item.director + "</p>");
              mediaItem.append("<p>🕒 " + item.length + "</p>");
          }
    
          const linkContainer = $('<div class="link-container">');
          linkContainer.append('<a href="view.php?id=' + item.id + '" target="_blank">📑</a>');
          linkContainer.append('<a href="edit.php?id=' + item.id + '" target="_blank">✏️</a>');
          linkContainer.append('<a href="delete.php?id=' + item.id + '" target="_blank">🗑️</a>');
          mediaItem.append(linkContainer);
      });
    
      currentlyWatchingItems.forEach(item => $(".media-container").append(item));
      favoriteItems.forEach(item => $(".media-container").append(item));
      normalItems.forEach(item => $(".media-container").append(item));
  }
  
  function displayWatchlistItems(data) {
    $(".watchlist").empty();
  
    $.each(data, function(index, item) {
        if (item.watchlist === "1") {
            const listItem = $('<li class="watchlist-item">');
  
            const viewButton = $('<button class="view-button">Update</button>');
            viewButton.on('click', function() {
                window.open('editWatchlist.php?id=' + item.id, '_blank');
            });
  
            const title = $('<span class="item-title">').text(item.title);
            const originTitle = $('<span class="item-origin-title">').text("''" + item.originTitle + "''"); 
            const director = $('<span class="item-director">').text("Genre: " + item.genre);
            
            const releaseDateString = item.releaseDate;
            const currentDate = new Date();
            const releaseDateR = new Date(releaseDateString);
  
            currentDate.setHours(0,0,0,0);
            releaseDateR.setHours(0,0,0,0);
            
            const length = releaseDateR < currentDate 
                ? $('<span class="item-length">').text("✅ Released on: " + item.releaseDate)
                : $('<span class="item-length">').text("❌ Not released (" + item.releaseDate + ")");
  
            const titleContainer = $('<div class="title-container"></div>');
            titleContainer.append(title);
  
            listItem.append(viewButton); 
            listItem.append(titleContainer);
            listItem.append(originTitle);
            listItem.append(director);
            listItem.append(length);
  
            const imageDisplay = $('<div class="image-display" style="display: none;"></div>');
            const image = $('<img class="item-image" src="' + item.coverUrl + '" alt="' + item.title + '" />');
            imageDisplay.append(image); 
            listItem.append(imageDisplay); 
  
            listItem.hover(
                function() {
                    imageDisplay.show(); 
                },
                function() {
                    imageDisplay.hide(); 
                }
            );
  
            $(".watchlist").append(listItem);
        }
    });
  }
  
  function searchMediaItems(query) {
    $.ajax({
        type: "GET",
        url: "search_media_items.php",
        data: { query: query },
        dataType: "json",
        success: function (data) {
          const filteredData = data.filter(item => item.watchlist !== "1");
            displayMediaItems(filteredData);
        },
        error: function (xhr, status, error) {
            console.error("Error searching media items:", error);
        }
    });
  }
  
  function addItem() {
    window.location.href = "add_item.php";
  }
  
  function checkWatchlist() {
    window.location.href = "watchlist.php";
  }
  
  function watched() {
    window.location.href = "index.html";
  }
  
  function sortMediaItemsAZ() {
    if (mediaItemsData.length === 0) {
        console.warn("No media items available to sort.");
        return;
    }
    const sortedData = mediaItemsData.slice().sort((a, b) => a.title.localeCompare(b.title)); 
    displayMediaItems(sortedData);
  }

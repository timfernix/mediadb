$(document).ready(function () {
    loadMediaItems();
    loadWatchlistItems();

    $("#search-bar").on("input", function () {
        searchMediaItems($(this).val());
    });

    $("#add-item-btn").on("click", addItem);
    $("#watchlist-btn").on("click", checkWatchlist);
    $("#watched-btn").on("click", watched);
    $("#sortAZ-btn").on("click", sortMediaItemsAZ);
});

let mediaItemsData = [];

function loadMediaItems() {
    fetchData("load_media_items.php", displayMediaItems, "Error loading media items:");
}

function loadWatchlistItems() {
    fetchData("load_media_items.php", displayWatchlistItems, "Error loading watchlist items:");
}

function fetchData(url, successCallback, errorMessage) {
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (data) {
            mediaItemsData = data; 
            if (successCallback === displayMediaItems) {
                updateSearchPlaceholder(data);
            }
            successCallback(data);
        },
        error: function (xhr, status, error) {
            console.error(errorMessage, error);
        }
    });
}

function updateSearchPlaceholder(data) {
    const counts = data.reduce((acc, item) => {
        acc[item.type] = (acc[item.type] || 0) + 1;
        acc.total++;
        
        let totalMinutes = 0;
        const duration = parseInt(item.duration, 10);
        const timesWatched = parseInt(item.timeswatched, 10);

        if (!isNaN(duration) && duration > 0 && !isNaN(timesWatched) && timesWatched > 0) {
            if (item.type === "Game") {
                totalMinutes = duration;
            } else if (item.type === "Series") {
                const episodesPerSeasonArray = item.episodesPerSeason ? item.episodesPerSeason.split(',').map(Number) : [];
                const totalEpisodes = episodesPerSeasonArray.reduce((sum, episodes) => sum + episodes, 0);
                totalMinutes = duration * totalEpisodes * timesWatched;
            } else if (item.type === "Movie") {
                totalMinutes = duration * timesWatched;
            }
        }

        console.log(`Total Minutes for ${item.title}: ${totalMinutes}`);
        
        acc.totalMinutes += totalMinutes;
        return acc;
    }, { total: 0, totalMinutes: 0 });

    console.log(`Total Minutes: ${counts.totalMinutes}`);

    const days = Math.floor(counts.totalMinutes / 1440);
    const hours = Math.floor((counts.totalMinutes % 1440) / 60);
    const minutes = counts.totalMinutes % 60;

    console.log(`Days: ${days}, Hours: ${hours}, Minutes: ${minutes}`);

    $("#search-bar").attr("placeholder", `🔍 Search ${counts.Movie || 0} movies, ${counts.Series || 0} series and ${counts.Game || 0} games (${counts.total} total, ${days}d ${hours}h ${minutes}m spent)`);
}


function displayMediaItems(data) {
    $(".media-container").empty();

    const categorizedItems = categorizeMediaItems(data);

    categorizedItems.currentlyWatching.forEach(item => $(".media-container").append(item));
    categorizedItems.favorite.forEach(item => $(".media-container").append(item));
    categorizedItems.normal.forEach(item => $(".media-container").append(item));
}

function categorizeMediaItems(data) {
    const categorized = {
        currentlyWatching: [],
        favorite: [],
        normal: []
    };

    data.forEach(item => {
        if (item.watchlist === "1") return;

        const mediaItem = createMediaItemElement(item);
        if (item.favorite === "1") {
            categorized.favorite.push(mediaItem);
        } else if (item.currentlyWatching === "1") {
            categorized.currentlyWatching.push(mediaItem);
        } else {
            categorized.normal.push(mediaItem);
        }
    });

    return categorized;
}

function createMediaItemElement(item) {
    const mediaItem = $('<div class="media-item">');
    const emoji = getEmojiForType(item.type);

    if (item.favorite === "1") {
        mediaItem.append('<div class="favorite">❤️ Favorite ❤️</div>');
    } else if (item.currentlyWatching === "1") {
        mediaItem.append('<div class="currently-watching">✨ Currently Watching ✨</div>');
    } else {
        mediaItem.append('<div class="watched">✅ ' + (item.type === "Game" ? "Played" : "Watched") + ' ✅</div>');
    }

    mediaItem.append(`
        <img src="${item.coverUrl}">
        <div class="title">${item.title}</div><br><br>
        <p>📢 <i>''${item.originTitle}''</i></p>
        ${emoji}<b>${item.type}</b>
        <p>🎭 ${item.genre}</p>
        <p>📆 ${item.releaseDate}</p>
    `);

    if (item.type === "Game") {
        mediaItem.append(`<p>👨‍💻 ${item.director}</p>`);
    } else {
        mediaItem.append(`<p>🎥 ${item.director}</p><p>🕒 ${item.length}</p>`);
    }

    const linkContainer = $('<div class="link-container">');
    linkContainer.append(`
        <a href="view.php?id=${item.id }" target="_blank">📑</a>
        <a href="edit.php?id=${item.id}" target="_blank">✏️</a>
        <a href="delete.php?id=${item.id}" target="_blank">🗑️</a>
    `);
    mediaItem.append(linkContainer);

    return mediaItem;
}

function getEmojiForType(type) {
    switch (type) {
        case "Movie": return "🎥 ";
        case "Series": return "📺 ";
        case "Game": return "🎮 ";
        default: return "";
    }
}

function displayWatchlistItems(data) {
    $(".watchlist").empty();

    data.forEach(item => {
        if (item.watchlist === "1") {
            const listItem = createWatchlistItemElement(item);
            $(".watchlist").append(listItem);
        }
    });
}

function createWatchlistItemElement(item) {
    const listItem = $('<li class="watchlist-item">');
    const viewButton = $('<button class="view-button">Update</button>').on('click', () => {
        window.open('edit.php?id=' + item.id, '_blank');
    });

    const title = $('<span class="item-title">').text(item.title);
    const originTitle = $('<span class="item-origin-title">').text("''" + item.originTitle + "''");
    const director = $('<span class="item-director">').text("Genre: " + item.genre);
    const length = getReleaseStatus(item.releaseDate);

    const titleContainer = $('<div class="title-container"></div>').append(title);
    listItem.append(viewButton, titleContainer, originTitle, director, length);

    const imageDisplay = $('<div class="image-display" style="display: none;"></div>').append(
        $('<img class="item-image" src="' + item.coverUrl + '" alt="' + item.title + '" />')
    );
    listItem.append(imageDisplay);

    listItem.hover(
        () => imageDisplay.show(),
        () => imageDisplay.hide()
    );

    return listItem;
}

function getReleaseStatus(releaseDateString) {
    const releaseDate = new Date(releaseDateString);
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);
    releaseDate.setHours(0, 0, 0, 0);

    return $('<span class="item-length">').text(releaseDate < currentDate
        ? "✅ Released on: " + releaseDateString
        : "❌ Not released (" + releaseDateString + ")");
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

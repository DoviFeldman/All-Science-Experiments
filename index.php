<?php
// RUN php -S 0.0.0.0:8000 to preview the site
// Science Video Learning Platform - Single PHP File
session_start();

// Sample CSV data structure - you can replace this with actual CSV file reading
$videos_data = [
    ['id' => 1, 'title' => 'Basic Chemistry Reactions', 'youtube_id' => 'dQw4w9WgXcQ', 'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', 'subject' => 'Chemistry', 'age' => '6-8', 'tools' => 'Test tubes,Beakers', 'cost' => 'Low', 'creator' => 'Science Bob'],
    ['id' => 2, 'title' => 'Physics Fun with Magnets', 'youtube_id' => 'dQw4w9WgXcQ', 'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', 'subject' => 'Physics', 'age' => '4-6', 'tools' => 'Magnets,Iron filings', 'cost' => 'Low', 'creator' => 'Physics Girl'],
    ['id' => 3, 'title' => 'Biology Cell Structure', 'youtube_id' => 'dQw4w9WgXcQ', 'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', 'subject' => 'Biology', 'age' => '10-12', 'tools' => 'Microscope,Slides', 'cost' => 'High', 'creator' => 'Biology Bytes'],
    ['id' => 4, 'title' => 'Simple Math Patterns', 'youtube_id' => 'dQw4w9WgXcQ', 'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', 'subject' => 'Math', 'age' => '2-4', 'tools' => 'Counting blocks', 'cost' => 'Low', 'creator' => 'Math Magic'],
    ['id' => 5, 'title' => 'Advanced Chemistry Lab', 'youtube_id' => 'dQw4w9WgXcQ', 'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', 'subject' => 'Chemistry', 'age' => '14-16', 'tools' => 'Bunsen burner,Lab equipment', 'cost' => 'High', 'creator' => 'Science Bob'],
    ['id' => 6, 'title' => 'Earth Science Weather', 'youtube_id' => 'dQw4w9WgXcQ', 'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', 'subject' => 'Earth Science', 'age' => '8-10', 'tools' => 'Thermometer,Rain gauge', 'cost' => 'Medium', 'creator' => 'Earth Explorer'],
];

// Get current filters
$selected_subject = isset($_GET['subject']) ? $_GET['subject'] : 'All';
$selected_age = isset($_GET['age']) ? $_GET['age'] : 'All';
$selected_creator = isset($_GET['creator']) ? $_GET['creator'] : '';
$selected_tools = isset($_GET['tools']) ? $_GET['tools'] : '';
$selected_cost = isset($_GET['cost']) ? $_GET['cost'] : '';
$playlist_view = isset($_GET['playlist']) ? $_GET['playlist'] : '';

// Check if viewing a single video
$video_id = isset($_GET['video_id']) ? intval($_GET['video_id']) : null;

// Filter videos based on selections
$filtered_videos = $videos_data;

if ($selected_subject !== 'All') {
    $filtered_videos = array_filter($filtered_videos, function($video) use ($selected_subject) {
        return strpos($video['subject'], $selected_subject) !== false;
    });
}

if ($selected_age !== 'All') {
    $filtered_videos = array_filter($filtered_videos, function($video) use ($selected_age) {
        return strpos($video['age'], $selected_age) !== false;
    });
}

if ($selected_creator) {
    $filtered_videos = array_filter($filtered_videos, function($video) use ($selected_creator) {
        return $video['creator'] === $selected_creator;
    });
}

if ($selected_tools) {
    $filtered_videos = array_filter($filtered_videos, function($video) use ($selected_tools) {
        return strpos($video['tools'], $selected_tools) !== false;
    });
}

if ($selected_cost) {
    $filtered_videos = array_filter($filtered_videos, function($video) use ($selected_cost) {
        return $video['cost'] === $selected_cost;
    });
}

// Get unique values for filters
$subjects = array_unique(array_column($videos_data, 'subject'));
$creators = array_unique(array_column($videos_data, 'creator'));
$all_tools = [];
foreach($videos_data as $video) {
    $tools = explode(',', $video['tools']);
    $all_tools = array_merge($all_tools, $tools);
}
$all_tools = array_unique($all_tools);
$costs = array_unique(array_column($videos_data, 'cost'));

// Age ranges
$age_ranges = ['2-4', '4-6', '6-8', '8-10', '10-12', '12-14', '14-16'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Science Experiments</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: #333;
        }

        .banner {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .banner h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .banner p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .filter-bar {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group label {
            font-weight: bold;
            color: #2a5298;
        }

        .filter-btn {
            background: white;
            border: 2px solid #2a5298;
            color: #2a5298;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }

        .filter-btn:hover, .filter-btn.active {
            background: #2a5298;
            color: white;
        }

        .expand-filters {
            background: #2a5298;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            margin-left: auto;
        }

        .advanced-filters {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .content-area {
            display: flex;
            gap: 20px;
        }

        .main-content {
            flex: 1;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .video-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
        }

        .video-info {
            padding: 15px;
        }

        .video-title {
            font-size: 1.1em;
            font-weight: bold;
            color: #2a5298;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .video-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }

        .left-sidebar {
    position: fixed;
    left: -200px;   /* was left: -250px;  */
    top: 0;
    width: 200px;   /* was width: 250px;  */
    height: 100vh;
    background: #f8f9fa;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    transition: left 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
    padding-top: 80px;
}

.left-sidebar.open {
    left: 0;
}

.creators-toggle {
    position: fixed;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #2a5298;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 0 10px 10px 0;
    cursor: pointer;
    z-index: 1001;
    font-size: 16px;
}

.creators-content {
    padding: 20px;
}

.main-container {
    transition: margin-left 0.3s ease;
}

.main-container.shifted {
    margin-left: 200px; /* was margin-left: 250px; */ 
}


        .playlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(42, 82, 152, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 14px;
        }

        .sidebar {
            width: 250px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .sidebar-section {
            margin-bottom: 25px;
        }

        .sidebar-title {
            font-weight: bold;
            color: #2a5298;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            user-select: none;
        }

        .sidebar-content {
            display: none;
            padding-left: 10px;
        }

        .sidebar-content.active {
            display: block;
        }

        .creator-item, .playlist-item {
            padding: 5px 10px;
            margin: 2px 0;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .creator-item:hover, .playlist-item:hover {
            background: #e9ecef;
        }

        .video-player-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }

        .video-player-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 900px;
            width: 100%;
        }

        .back-btn {
            background: #2a5298;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            text-decoration: none;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .content-area {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: static;
            }
            
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                justify-content: center;
            }
        }
    </style>
</head>
<body>


<button class="creators-toggle" onclick="toggleLeftSidebar()">👥</button>

<div class="left-sidebar" id="leftSidebar">
    <div class="creators-content">
        <h3 style="color: #2a5298; margin-bottom: 15px; text-align: left; margin-left: 20px;">Creators</h3> <!-- was text-align: center --> <!-- margin-left: 20px; i added this, i like it. -->
        <?php foreach($creators as $creator): ?>
            <div class="creator-item">
                <a href="?creator=<?php echo urlencode($creator); ?>" style="text-decoration: none; color: inherit;">
                    <?php echo htmlspecialchars($creator); ?>
                </a>
            </div>
        <?php endforeach; ?>
        <?php if($selected_creator): ?>
            <div class="creator-item">
                <a href="?" style="text-decoration: none; color: #2a5298;">← Show All Creators</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($video_id): ?>
    <!-- Single Video View -->
    <?php 
    $current_video = null;
    foreach($videos_data as $video) {
        if ($video['id'] == $video_id) {
            $current_video = $video;
            break;
        }
    }
    ?>
    
    <?php if ($current_video): ?>
    <div class="banner">
        <h1><?php echo htmlspecialchars($current_video['title']); ?></h1>
    </div>
    
    <div class="video-player-page">
        <div class="video-player-container">
            <a href="?" class="back-btn">← Back to Videos</a>
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe 
                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($current_video['youtube_id']); ?>" 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                    allowfullscreen>
                </iframe>
            </div>
            <div style="margin-top: 20px;">
                <h2><?php echo htmlspecialchars($current_video['title']); ?></h2>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($current_video['subject']); ?></p>
                <p><strong>Age Range:</strong> <?php echo htmlspecialchars($current_video['age']); ?></p>
                <p><strong>Creator:</strong> <?php echo htmlspecialchars($current_video['creator']); ?></p>
                <p><strong>Tools Used:</strong> <?php echo htmlspecialchars($current_video['tools']); ?></p>
                <p><strong>Cost Level:</strong> <?php echo htmlspecialchars($current_video['cost']); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Main Page View -->
    <div class="banner">
        <h1><?php echo $selected_creator ? htmlspecialchars($selected_creator) . ' - Science Videos' : 'Science Learning Platform'; ?></h1>
        <p><?php echo $selected_creator ? 'Explore educational videos by ' . htmlspecialchars($selected_creator) : 'Discover amazing science videos for all ages'; ?></p>
    </div>

    <div class="main-container">
        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <label>Subject:</label>
                <a href="?subject=All&age=<?php echo urlencode($selected_age); ?>" 
                   class="filter-btn <?php echo $selected_subject == 'All' ? 'active' : ''; ?>">All</a>
                <?php foreach($subjects as $subject): ?>
                    <a href="?subject=<?php echo urlencode($subject); ?>&age=<?php echo urlencode($selected_age); ?>" 
                       class="filter-btn <?php echo $selected_subject == $subject ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($subject); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="filter-group">
                <label>Age:</label>
                <a href="?subject=<?php echo urlencode($selected_subject); ?>&age=All" 
                   class="filter-btn <?php echo $selected_age == 'All' ? 'active' : ''; ?>">All</a>
                <?php foreach($age_ranges as $age): ?>
                    <a href="?subject=<?php echo urlencode($selected_subject); ?>&age=<?php echo urlencode($age); ?>" 
                       class="filter-btn <?php echo $selected_age == $age ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($age); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <button class="expand-filters" onclick="toggleAdvancedFilters()">More Filters</button>

            <div class="advanced-filters" id="advancedFilters">
                <div class="filter-group">
                    <label>Tools:</label>
                    <a href="?subject=<?php echo urlencode($selected_subject); ?>&age=<?php echo urlencode($selected_age); ?>&tools=" 
                       class="filter-btn <?php echo !$selected_tools ? 'active' : ''; ?>">All</a>
                    <?php foreach($all_tools as $tool): ?>
                        <a href="?subject=<?php echo urlencode($selected_subject); ?>&age=<?php echo urlencode($selected_age); ?>&tools=<?php echo urlencode($tool); ?>" 
                           class="filter-btn <?php echo $selected_tools == $tool ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars(trim($tool)); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="filter-group">
                    <label>Cost:</label>
                    <a href="?subject=<?php echo urlencode($selected_subject); ?>&age=<?php echo urlencode($selected_age); ?>&cost=" 
                       class="filter-btn <?php echo !$selected_cost ? 'active' : ''; ?>">All</a>
                    <?php foreach($costs as $cost): ?>
                        <a href="?subject=<?php echo urlencode($selected_subject); ?>&age=<?php echo urlencode($selected_age); ?>&cost=<?php echo urlencode($cost); ?>" 
                           class="filter-btn <?php echo $selected_cost == $cost ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cost); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="content-area">
            <!-- Main Content -->
            <div class="main-content">
                <div class="video-grid">
                    <?php foreach($filtered_videos as $video): ?>
                        <div class="video-card">
                            <button class="playlist-btn" onclick="addToPlaylist(<?php echo $video['id']; ?>, '<?php echo htmlspecialchars($video['title']); ?>')">+</button>
                            
                            <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" 
                                 alt="<?php echo htmlspecialchars($video['title']); ?>"
                                 class="video-thumbnail"
                                 onclick="window.open('?video_id=<?php echo $video['id']; ?>', '_blank')">
                            
                            <div class="video-info">
                                <div class="video-title" onclick="window.open('?video_id=<?php echo $video['id']; ?>', '_blank')">
                                    <?php echo htmlspecialchars($video['title']); ?>
                                </div>
                                <div class="video-meta">Subject: <?php echo htmlspecialchars($video['subject']); ?></div>
                                <div class="video-meta">Age: <?php echo htmlspecialchars($video['age']); ?></div>
                                <div class="video-meta">Creator: <?php echo htmlspecialchars($video['creator']); ?></div>
                                <div class="video-meta">Tools: <?php echo htmlspecialchars($video['tools']); ?></div>
                                <div class="video-meta">Cost: <?php echo htmlspecialchars($video['cost']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($filtered_videos)): ?>
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <h3>No videos found</h3>
                        <p>Try adjusting your filters to find more videos.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Creator right Sidebar, i deleted this and made the creator sidebar on the left  -->

                <div class="sidebar-section">
                    <div class="sidebar-title" onclick="toggleSidebar('playlists')">My Playlists</div>
                    <div class="sidebar-content" id="playlists">
                        <div style="margin-bottom: 10px;">
                            <input type="text" id="newPlaylistName" placeholder="Playlist name" style="width: 100%; padding: 5px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 3px;">
                            <button onclick="createPlaylist()" style="width: 100%; padding: 5px; background: #2a5298; color: white; border: none; border-radius: 3px; cursor: pointer;">Create Playlist</button>
                        </div>
                        <div id="playlistList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Advanced filters toggle
function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    filters.style.display = filters.style.display === 'block' ? 'none' : 'block';
}

// Sidebar toggle
function toggleSidebar(section) {
    const content = document.getElementById(section);
    content.classList.toggle('active');
}


function toggleLeftSidebar() {
    const sidebar = document.getElementById('leftSidebar');
    const mainContainer = document.querySelector('.main-container');
    
    sidebar.classList.toggle('open');
    mainContainer.classList.toggle('shifted');
}

// // Close sidebar when clicking outside // I COULD DELETE THIS IF I DONT LIKE IT!!!!!!!!!!
// document.addEventListener('click', function(event) {
//     const sidebar = document.getElementById('leftSidebar');
//     const toggle = document.querySelector('.creators-toggle');
    
//     if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
//         sidebar.classList.remove('open');
//         document.querySelector('.main-container').classList.remove('shifted');
//     }
// });


// Playlist functionality
let playlists = JSON.parse(localStorage.getItem('sciencePlaylists') || '{}');

function createPlaylist() {
    const name = document.getElementById('newPlaylistName').value.trim();
    if (name && !playlists[name]) {
        playlists[name] = [];
        localStorage.setItem('sciencePlaylists', JSON.stringify(playlists));
        document.getElementById('newPlaylistName').value = '';
        updatePlaylistDisplay();
    }
}

function addToPlaylist(videoId, videoTitle) {
    const playlistName = prompt('Enter playlist name (or create new):');
    if (playlistName) {
        if (!playlists[playlistName]) {
            playlists[playlistName] = [];
        }
        
        // Check if video already exists in playlist
        if (!playlists[playlistName].find(v => v.id === videoId)) {
            playlists[playlistName].push({id: videoId, title: videoTitle});
            localStorage.setItem('sciencePlaylists', JSON.stringify(playlists));
            alert('Video added to playlist: ' + playlistName);
            updatePlaylistDisplay();
        } else {
            alert('Video already exists in this playlist.');
        }
    }
}

function removeFromPlaylist(playlistName, videoId) {
    playlists[playlistName] = playlists[playlistName].filter(v => v.id !== videoId);
    if (playlists[playlistName].length === 0) {
        delete playlists[playlistName];
    }
    localStorage.setItem('sciencePlaylists', JSON.stringify(playlists));
    updatePlaylistDisplay();
}

function deletePlaylist(playlistName) {
    if (confirm('Delete playlist "' + playlistName + '"?')) {
        delete playlists[playlistName];
        localStorage.setItem('sciencePlaylists', JSON.stringify(playlists));
        updatePlaylistDisplay();
    }
}

function sharePlaylist(playlistName) {
    const playlistData = encodeURIComponent(JSON.stringify(playlists[playlistName]));
    const shareUrl = window.location.origin + window.location.pathname + '?shared_playlist=' + playlistData;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(shareUrl).then(() => {
            alert('Playlist link copied to clipboard!');
        });
    } else {
        prompt('Copy this link to share your playlist:', shareUrl);
    }
}

function updatePlaylistDisplay() {
    const container = document.getElementById('playlistList');
    let html = '';
    
    for (const [name, videos] of Object.entries(playlists)) {
        html += `
            <div style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px;">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 5px;">
                    <strong>${name}</strong>
                    <div>
                        <button onclick="sharePlaylist('${name}')" style="background: #28a745; color: white; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer; margin-right: 5px; font-size: 12px;">Share</button>
                        <button onclick="deletePlaylist('${name}')" style="background: #dc3545; color: white; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;">Delete</button>
                    </div>
                </div>
                <div style="font-size: 12px; color: #666;">${videos.length} videos</div>
        `;
        
        for (const video of videos) {
            html += `
                <div style="font-size: 11px; padding: 2px 0; display: flex; justify-content: space-between; align-items: center;">
                    <span onclick="window.open('?video_id=${video.id}', '_blank')" style="cursor: pointer; color: #2a5298;">${video.title}</span>
                    <button onclick="removeFromPlaylist('${name}', ${video.id})" style="background: #dc3545; color: white; border: none; padding: 1px 5px; border-radius: 2px; cursor: pointer; font-size: 10px;">×</button>
                </div>
            `;
        }
        
        html += '</div>';
    }
    
    container.innerHTML = html;
}

// Initialize playlist display
document.addEventListener('DOMContentLoaded', function() {
    updatePlaylistDisplay();
    
    // Check for shared playlist
    const urlParams = new URLSearchParams(window.location.search);
    const sharedPlaylist = urlParams.get('shared_playlist');
    if (sharedPlaylist) {
        try {
            const playlistData = JSON.parse(decodeURIComponent(sharedPlaylist));
            const playlistName = prompt('Name for this shared playlist:');
            if (playlistName && !playlists[playlistName]) {
                playlists[playlistName] = playlistData;
                localStorage.setItem('sciencePlaylists', JSON.stringify(playlists));
                updatePlaylistDisplay();
                alert('Shared playlist added successfully!');
            }
        } catch (e) {
            console.error('Error loading shared playlist:', e);
        }
    }
});
</script>

</body>
</html>
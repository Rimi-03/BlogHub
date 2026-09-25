<?php
include("db.php");

// Safe fallback page variable initialization
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BlogHub — Ideas in motion</title>
  <script>
    (function () {
      var savedTheme = null;

      try {
        savedTheme = localStorage.getItem('theme');
      } catch (error) {
        savedTheme = null;
      }

      var preferredTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      var theme = savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : preferredTheme;
      document.documentElement.classList.toggle('dark', theme === 'dark');
      document.documentElement.classList.toggle('light', theme === 'light');
      document.documentElement.dataset.theme = theme;
      document.documentElement.style.colorScheme = theme;
    }());
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="theme.css">
  <script src="theme.js" defer></script>
</head>

<body class="theme-page bg-gray-100">

  <nav class="theme-nav bg-white shadow">
    <div class="theme-nav-inner container mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">

      <h1 class="theme-brand font-bold text-2xl">
        <a href="index.php?page=home" class="hover:text-blue-600">
          BlogHub
        </a>
      </h1>

      <div class="flex flex-wrap justify-center items-center gap-4 text-sm md:text-base">
        <a href="index.php?page=home" class="theme-nav-link hover:text-gray-600">
          Home
        </a>

        <div class="relative group">
          <button type="button" class="theme-nav-button hover:text-gray-600">
            Blogs
          </button>

          <div class="theme-dropdown absolute hidden group-hover:block bg-white border shadow-md rounded mt-1 w-40 z-50">
            <a href="index.php?page=all" class="theme-dropdown-link block px-4 py-2 hover:bg-gray-100">
              All Blogs
            </a>

            <a href="index.php?page=popular" class="theme-dropdown-link block px-4 py-2 hover:bg-gray-100">
              Popular Blogs
            </a>
          </div>
        </div>

        <button type="button" data-theme-toggle class="theme-toggle" aria-label="Switch to dark theme" aria-pressed="false" title="Switch to dark theme">
          <svg class="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.4 15.2A8.5 8.5 0 018.8 3.6 8.5 8.5 0 1020.4 15.2z" />
          </svg>
          <svg class="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="12" r="4" />
            <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3l1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3l1.42-1.42" />
          </svg>
        </button>

      </div>

    </div>
  </nav>

  <?php if ($page == 'home') { ?>
    <section class="theme-hero bg-[url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRyPzfJ5ub8Yrp36q4Py4WIqiZ6x_Q4ftcRfHmlOAsTVj-BB6AsFTwHlUA&s=10')] bg-cover bg-center bg-gray-100 py-10 md:py-16 px-4 md:px-8 rounded-b shadow-md">
      <div class="theme-hero-inner container mx-auto px-4 md:px-6 flex flex-col md:flex-row items-stretch gap-8 md:gap-12">

        <div class="theme-hero-panel w-full md:w-1/2 bg-white/75 backdrop-blur-sm p-6 md:p-8 rounded-lg shadow-sm flex flex-col justify-center">
          <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
            <?= htmlspecialchars(getContent('hero_title')) ?>
          </h1>
          <p class="text-gray-600 text-base md:text-lg">
            <?= htmlspecialchars(getContent('hero_description')) ?> </p>
        </div>

        <div class="w-full md:w-1/2 flex min-h-[300px] md:min-h-full">
          <img
            src="<?= htmlspecialchars(getContent('hero_image')) ?>" alt="Blog Hero"
            class="theme-hero-image rounded-lg shadow-lg w-full h-full object-cover" />
        </div>

      </div>
    </section>

    <div class="px-4 py-6">
      <form action="index.php" method="GET" class="theme-search-form max-w-3xl mx-auto">
        <input type="hidden" name="page" value="search" />
        <div class="relative">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5 absolute right-3 top-3.5 text-gray-400 pointer-events-none z-10"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>

          <input type="text" id="searchInput" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search blogs..." autocomplete="off"
            class="theme-input w-full pl-4 pr-10 py-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

          <div
            id="suggestions"
            class="theme-suggestions absolute w-full bg-white border rounded-lg shadow-lg mt-1 hidden z-50 text-left overflow-hidden">
          </div>
        </div>
      </form>
    </div>
  <?php } ?>

  <?php if ($page == 'home') { ?>

    <div class="theme-section theme-section-tint px-4 py-8 md:p-10 bg-blue-100">
      <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="theme-section-title text-2xl font-bold text-gray-800">
            <?= htmlspecialchars(getContent('popular_title')) ?>
          </h2>

          <a href="index.php?page=popular"
            class="theme-link text-blue-600 hover:underline font-medium">
            View All
          </a>
        </div>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            WHERE blogs.views >= 5
            ORDER BY blogs.views DESC
            LIMIT 4
        ");
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <?php if ($result->num_rows > 0) { ?>
            <?php while ($blog = $result->fetch_assoc()) {
              $imgUrl = $blog['cover_image'];
            ?>
              <div onclick="window.location.href='index.php?page=single&id=<?= $blog['blog_id']; ?>'" class="theme-card theme-card-clickable bg-white rounded shadow overflow-hidden flex flex-col justify-between cursor-pointer hover:shadow-xl transition duration-200 group">
                <img src="<?= htmlspecialchars($imgUrl); ?>" class="theme-card-image w-full h-48 object-cover" />
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="theme-card-title font-bold text-gray-800 text-lg mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                    <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                  </div>
                  <div class="mt-4">
                    <div class="flex justify-between items-center mb-2">
                      <p class="text-xs text-gray-400"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                      <span class="theme-badge theme-badge-accent text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-bold border border-blue-100"><?= number_format($blog['views']) ?> views</span>
                    </div>
                    <span class="theme-card-link text-blue-600 hover:underline font-semibold block">Read More</span>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <p class="theme-empty-copy text-gray-500 italic col-span-full py-4">No trending topics available right now. Check back later!</p>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="theme-section px-4 py-8 md:p-10 bg-gray-50">
      <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="theme-section-title text-2xl font-bold text-gray-800"><?= htmlspecialchars(getContent('recent_title')) ?></h2>
          <a href="index.php?page=all" class="theme-link text-blue-600 hover:underline font-medium">View All</a>
        </div>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            ORDER BY blogs.publish_date DESC
            LIMIT 6
        ");
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while ($blog = $result->fetch_assoc()) {
            $imgUrl = $blog['cover_image'];
          ?>
            <div onclick="window.location.href='index.php?page=single&id=<?= $blog['blog_id']; ?>'" class="theme-card theme-card-clickable bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between cursor-pointer hover:shadow-xl transition duration-200 group">
              <img src="<?= htmlspecialchars($imgUrl); ?>" class="theme-card-image w-full h-48 object-cover">
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="theme-card-title font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                  <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                </div>
                <div class="mt-4">
                  <div class="flex justify-between items-center mb-2">
                    <p class="text-xs text-gray-400"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                    <span class="theme-badge text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium"><?= number_format($blog['views']) ?> views</span>
                  </div>
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="theme-card-link text-blue-600 hover:underline font-semibold block">Read More</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php } elseif ($page == 'search') { ?>

    <div class="theme-section p-10 bg-gray-50">
      <div class="container mx-auto">
        <?php
        $search_query = $_GET['q'] ?? '';
        ?>
        <div class="relative mb-6 flex items-center justify-center">
          <a href="index.php?page=home" class="absolute left-0 p-1 text-gray-600 hover:text-gray-900 transition-colors" aria-label="Go back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
          </a>

          <h2 class="theme-section-title text-3xl font-bold text-gray-800">Search Results</h2>
        </div>
        <p class="text-gray-500 mb-8" placeholder="Search blogs..."> <span class="theme-link font-semibold text-blue-600">"<?= htmlspecialchars($search_query); ?>"</span></p>

        <?php
        $like_term = "%" . $search_query . "%";

        $stmt = $conn->prepare("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            WHERE blogs.title LIKE ? 
               OR blogs.subtitle LIKE ? 
               OR author.username LIKE ?
            ORDER BY blogs.publish_date DESC
        ");
        $stmt->bind_param("sss", $like_term, $like_term, $like_term);
        $stmt->execute();
        $search_result = $stmt->get_result();
        $stmt->close();

        if ($search_result->num_rows > 0) {
        ?>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($blog = $search_result->fetch_assoc()) {
              $imgUrl = $blog['cover_image'];
            ?>
              <div onclick="window.location.href='index.php?page=single&id=<?= $blog['blog_id']; ?>'" class="theme-card theme-card-clickable bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between cursor-pointer hover:shadow-xl transition duration-200 group">
                <img src="<?= htmlspecialchars($imgUrl); ?>" class="theme-card-image w-full h-48 object-cover">
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="theme-card-title font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                    <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                  </div>
                  <div class="mt-4">
                    <div class="flex justify-between items-center mb-2">
                      <p class="text-xs text-gray-400">
                        <?= date('M d, Y', strtotime($blog['publish_date'])); ?>
                      </p>

                      <span class="theme-badge text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium">
                        <?= number_format($blog['views']) ?> views
                      </span>
                    </div>

                    <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>"
                      class="theme-card-link text-blue-600 hover:underline font-semibold block">
                      Read More
                    </a>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } else { ?>
          <div class="theme-empty-state text-center py-16 bg-white rounded-lg shadow-sm max-w-xl mx-auto p-6">
            <p class="text-xl font-bold text-gray-700 mb-2">No matching posts found</p>
            <p class="text-gray-400 mb-6">We couldn't find any articles containing your keywords. Try refining your spelling or searching for a different phrase.</p>
            <a href="index.php?page=home" class="theme-button theme-button-primary text-sm bg-blue-600 text-white font-medium px-4 py-2 rounded shadow hover:bg-blue-700 transition">View Home Feed</a>
          </div>
        <?php } ?>
      </div>
    </div>
  <?php } elseif ($page == 'popular') { ?>

    <div class="theme-section p-10 bg-blue-50">
      <div class="container mx-auto">
        <h2 class="theme-section-title text-3xl font-bold text-gray-800 mb-6">Trending & Popular Blogs</h2>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            WHERE blogs.views > 5
            ORDER BY blogs.views DESC
        ");
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php if ($result->num_rows > 0) { ?>
            <?php while ($blog = $result->fetch_assoc()) {
              $imgUrl = $blog['cover_image'];
            ?>
              <div onclick="window.location.href='index.php?page=single&id=<?= $blog['blog_id']; ?>'" class="theme-card theme-card-clickable bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between cursor-pointer hover:shadow-xl transition duration-200 group">
                <img src="<?= htmlspecialchars($imgUrl); ?>" class="theme-card-image w-full h-48 object-cover">
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="theme-card-title font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                    <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                  </div>
                  <div class="mt-4">
                    <div class="flex justify-between items-center mb-2">
                      <p class="text-xs text-gray-400"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                      <span class="theme-badge theme-badge-accent text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-bold border border-blue-100"><?= number_format($blog['views']) ?> views</span>
                    </div>
                    <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="theme-card-link text-blue-600 hover:underline font-semibold block">Read More</a>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <p class="theme-empty-copy text-gray-500 italic col-span-full py-4">No trending topics available right now. Check back later!</p>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php } elseif ($page == 'all') { ?>

    <div class="theme-section p-10 bg-gray-50">
      <div class="container mx-auto">
        <h2 class="theme-section-title text-3xl font-bold text-gray-800 mb-6">All Blog Posts</h2>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            ORDER BY blogs.publish_date DESC
        ");
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while ($blog = $result->fetch_assoc()) {
            $imgUrl = $blog['cover_image'];
          ?>
            <div onclick="window.location.href='index.php?page=single&id=<?= $blog['blog_id']; ?>'" class="theme-card theme-card-clickable bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between cursor-pointer hover:shadow-xl transition duration-200 group">
              <img src="<?= htmlspecialchars($imgUrl); ?>" class="theme-card-image w-full h-48 object-cover">
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="theme-card-title font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                  <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                </div>
                <div class="mt-4">
                  <div class="flex justify-between items-center mb-2">
                    <p class="text-xs text-gray-400"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                    <span class="theme-badge text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium"><?= number_format($blog['views']) ?> views</span>
                  </div>
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="theme-card-link text-blue-600 hover:underline font-semibold block">Read More</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php } elseif ($page == 'single') { ?>

    <?php
    $blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($blog_id > 0) {
      $conn->query("UPDATE blogs SET views = views + 1 WHERE blog_id = $blog_id");
    }

    $stmt = $conn->prepare("
        SELECT blogs.*, author.username 
        FROM blogs 
        JOIN author ON blogs.author_id = author.author_id 
        WHERE blogs.blog_id = ?
    ");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $blog_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($blog_data) {
      // Fetch structural extra attachments registry arrays
      $gallery_result = $conn->query("SELECT image_path FROM blog_images WHERE blog_id = $blog_id");

      // Seed JavaScript media engine list safely using PHP runtime array injections
      $mediaDeck = [];
      if (!empty($blog_data['cover_image'])) {
        $mediaDeck[] = $blog_data['cover_image'];
      }
      if ($gallery_result) {
        while ($photo = $gallery_result->fetch_assoc()) {
          $mediaDeck[] = $photo['image_path'];
        }
      }
    ?>
      <article class="theme-article py-6 md:py-12 px-4 md:px-6 max-w-4xl mx-auto bg-white my-8 rounded-xl shadow-sm">
        <div class="relative h-10 flex items-center mb-4">
          <a href="index.php?page=home" class="absolute left-0 p-1 text-gray-600 hover:text-gray-900 transition-colors" aria-label="Go back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
          </a>
        </div>

        <div class="theme-media-frame relative w-full h-[260px] sm:h-[450px] rounded-xl overflow-hidden border bg-gray-950 mb-8 group">
          <img id="singleViewCarouselDisplay" src="<?= htmlspecialchars($mediaDeck[0] ?? 'uploads/default.jpg'); ?>" class="w-full h-full object-contain transition-all duration-300">

          <?php if (count($mediaDeck) > 1): ?>
            <button onclick="advanceSingleViewCarouselNext()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-2xl transition select-none z-10 focus:outline-none" title="Next Image">
              &gt;
            </button>
            <div class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-3 py-1 rounded-md font-mono tracking-wider select-none" id="singleViewCarouselCounter">
              1 / <?= count($mediaDeck); ?>
            </div>
          <?php endif; ?>
        </div>

        <script>
          const singleCarouselDeck = <?= json_encode($mediaDeck); ?>;
          let singleCarouselIndex = 0;

          function advanceSingleViewCarouselNext() {
            if (singleCarouselDeck.length <= 1) return;

            // Infinite Index Loop Wrapper
            singleCarouselIndex = (singleCarouselIndex + 1) % singleCarouselDeck.length;

            document.getElementById("singleViewCarouselDisplay").src = singleCarouselDeck[singleCarouselIndex];
            document.getElementById("singleViewCarouselCounter").textContent = `${singleCarouselIndex + 1} / ${singleCarouselDeck.length}`;
          }
        </script>

        <h1 class="theme-article-title text-2xl md:text-5xl font-black text-gray-900 leading-tight mb-2">
          <?= htmlspecialchars($blog_data['title']); ?>
        </h1>

        <h2 class="theme-article-subtitle text-lg md:text-2xl text-gray-500 font-medium mb-6 italic">
          <?= htmlspecialchars($blog_data['subtitle']); ?>
        </h2>

        <div class="theme-meta flex items-center gap-3 border-y border-gray-200 py-3 mb-8">
          <div class="text-sm">
            <p class="font-bold text-gray-800">By <?= htmlspecialchars($blog_data['username']); ?></p>
            <p class="text-gray-400 text-xs">Published on: <?= date('F d, Y', strtotime($blog_data['publish_date'])); ?></p>
          </div>
          <div class="theme-badge theme-badge-accent bg-blue-50 border border-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5">
            <span><?= number_format($blog_data['views']) ?> Views</span>
          </div>
        </div>

        <div class="theme-summary bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-8">
          <p class="font-bold text-blue-950 text-xs uppercase tracking-wider mb-1">Quick Summary Description:</p>
          <p class="text-gray-700 italic text-base">
            <?= htmlspecialchars($blog_data['description']); ?>
          </p>
        </div>

        <div class="theme-article-body text-gray-800 text-base md:text-lg leading-relaxed whitespace-pre-line font-serif mb-10">
          <?= htmlspecialchars($blog_data['content']); ?>
        </div>

      </article>
    <?php } else { ?>
      <div class="theme-empty-state text-center py-20 bg-white m-10 rounded shadow max-w-md mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Article Not Found</h2>
        <p class="text-gray-500 mb-6">The article you requested could not be found or has been moved.</p>
        <a href="index.php?page=home" class="theme-button theme-button-primary bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition">Return to Home</a>
      </div>
    <?php } ?>
  <?php } ?>

  <footer class="theme-footer bg-white mt-10 border-t">
    <div class="container mx-auto px-4 py-6 text-center">
      <p class="text-gray-600 text-sm md:text-base">
        <?= htmlspecialchars(getContent('footer_text')) ?>
      </p>
    </div>
  </footer>
  <script src="index.js"></script>

</body>

</html>
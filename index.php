<?php
include("db.php");
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BLOGHUB</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

  <!-- nav bar -->
  <nav class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="font-bold text-xl">
      <a href="index.php?page=home" class="hover:text-blue-600">BlogHub</a>
    </h1>

    <div class="flex items-center space-x-4">
      <a href="index.php?page=home" class="hover:text-gray-600">Home</a>
      <div class="relative group">
        <button class="hover:text-gray-600 focus:outline-none">Blogs</button>
        <div class="absolute hidden group-hover:block bg-white border shadow-md rounded mt-1 w-32 py-1 z-50">
          <a href="index.php?page=all" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-800">All Blogs</a>
          <a href="index.php?page=popular" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-800">Popular</a>
        </div>
      </div>
      <a href="index.php?page=home" class="text-blue-600 font-medium hover:underline">Create Post</a>
    </div>
  </nav>

  <?php if ($page == 'home') { ?>
    <!-- Hero section -->
    <section class="bg-[url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRyPzfJ5ub8Yrp36q4Py4WIqiZ6x_Q4ftcRfHmlOAsTVj-BB6AsFTwHlUA&s=10')] bg-cover bg-center bg-gray-100 py-20 px-8 rounded-b shadow-md">
      <div class="container mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2 bg-white/75 backdrop-blur-sm p-6 rounded-lg shadow-sm">
          <h1 class="text-5xl font-bold text-gray-800 mb-4">
            Share Your Ideas With The World
          </h1>
          <p class="text-gray-600 text-lg mb-6">
            A modern blogging platform where users can create, read, search, and explore blogs. It highlights popular posts and provides a clean interface for publishing and browsing content.
          </p>
        </div>

        <div class="md:w-1/2 mt-10 md:mt-0">
          <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a" alt="Blog Hero" class="rounded-lg shadow-lg w-full" />
        </div>
      </div>
    </section>

    <!-- Search bar -->
    <div class="p-4 text-center mt-4">
      <form action="index.php" method="GET" class="max-w-2xl mx-auto">
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
            class="w-full pl-4 pr-10 py-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

          <div
            id="suggestions"
            class="absolute w-full bg-white border rounded-lg shadow-lg mt-1 hidden z-50 text-left overflow-hidden">
          </div>
        </div>
      </form>
    </div>
  <?php } ?>

  <?php if ($page == 'home') { ?>

    <div class="p-10 bg-blue-100">
      <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Popular Blogs</h2>
          <a href="index.php?page=popular" class="text-blue-600 hover:underline font-medium">View All</a>
        </div>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            WHERE blogs.is_popular = 1
            LIMIT 4
        ");
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <?php while ($blog = $result->fetch_assoc()) { ?>
            <div class="bg-white rounded shadow overflow-hidden flex flex-col justify-between">
              <img src="<?= htmlspecialchars($blog['cover_image']); ?>" class="w-full h-48 object-cover" />
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-bold text-gray-800 text-lg mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                  <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                </div>
                <div class="mt-4">
                  <p class="text-xs text-gray-400 mb-2"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="p-10 bg-gray-50">
      <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Recent Stories</h2>
          <a href="index.php?page=all" class="text-blue-600 hover:underline font-medium">View All</a>
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
          <?php while ($blog = $result->fetch_assoc()) { ?>
            <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between">
              <img src="<?= htmlspecialchars($blog['cover_image']); ?>" class="w-full h-48 object-cover">
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                  <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                </div>
                <div class="mt-4">
                  <p class="text-xs text-gray-400 mb-2"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php } elseif ($page == 'search') { ?>

    <div class="p-10 bg-gray-50">
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

          <h2 class="text-3xl font-bold text-gray-800">Search Results</h2>
        </div>
        <p class="text-gray-500 mb-8">Showing matching results for: <span class="font-semibold text-blue-600">"<?= htmlspecialchars($search_query); ?>"</span></p>

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
            <?php while ($blog = $search_result->fetch_assoc()) { ?>
              <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between">
                <img src="<?= htmlspecialchars($blog['cover_image']); ?>" class="w-full h-48 object-cover">
                <div class="p-4 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                    <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                  </div>
                  <div class="mt-4">
                    <p class="text-xs text-gray-400 mb-2"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                    <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More</a>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } else { ?>
          <div class="text-center py-16 bg-white rounded-lg shadow-sm max-w-xl mx-auto p-6">
            <p class="text-xl font-bold text-gray-700 mb-2">No matching posts found</p>
            <p class="text-gray-400 mb-6">We couldn't find any articles containing your keywords. Try refining your spelling or searching for a different phrase.</p>
            <a href="index.php?page=home" class="text-sm bg-blue-600 text-white font-medium px-4 py-2 rounded shadow hover:bg-blue-700 transition">View Home Feed</a>
          </div>
        <?php } ?>
      </div>
    </div>
  <?php } elseif ($page == 'popular') { ?>

    <div class="p-10 bg-blue-50">
      <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Trending & Popular Blogs</h2>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            WHERE blogs.is_popular = 1
            ORDER BY blogs.publish_date DESC
        ");
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while ($blog = $result->fetch_assoc()) { ?>
            <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between">
              <img src="<?= htmlspecialchars($blog['cover_image']); ?>" class="w-full h-48 object-cover">
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                  <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                </div>
                <div class="mt-4">
                  <p class="text-xs text-gray-400 mb-2"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php } elseif ($page == 'all') { ?>

    <div class="p-10 bg-gray-50">
      <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">All Blog Posts</h2>

        <?php
        $result = $conn->query("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            ORDER BY blogs.publish_date DESC
        ");
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while ($blog = $result->fetch_assoc()) { ?>
            <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col justify-between">
              <img src="<?= htmlspecialchars($blog['cover_image']); ?>" class="w-full h-48 object-cover">
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($blog['title']); ?></h3>
                  <p class="text-xs text-gray-500 font-medium">By <?= htmlspecialchars($blog['username']); ?></p>
                </div>
                <div class="mt-4">
                  <p class="text-xs text-gray-400 mb-2"><?= date('M d, Y', strtotime($blog['publish_date'])); ?></p>
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More</a>
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
    ?>
      <article class="py-12 px-6 max-w-4xl mx-auto bg-white my-8 rounded-xl shadow-sm">
        <div class="relative h-10 flex items-center mb-4">
          <a href="index.php?page=home" class="absolute left-0 p-1 text-gray-600 hover:text-gray-900 transition-colors" aria-label="Go back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </div>

        </a>
        <?php if (!empty($blog_data['cover_image'])): ?>
          <img src="<?= htmlspecialchars($blog_data['cover_image']); ?>" alt="Cover Banner" class="w-full h-[450px] object-cover rounded-xl shadow-sm mb-8" />
        <?php endif; ?>

        <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight mb-2">
          <?= htmlspecialchars($blog_data['title']); ?>
        </h1>

        <h2 class="text-xl md:text-2xl text-gray-500 font-medium mb-6 italic">
          <?= htmlspecialchars($blog_data['subtitle']); ?>
        </h2>

        <div class="flex items-center gap-3 border-y border-gray-200 py-3 mb-8">
          <div class="text-sm">
            <p class="font-bold text-gray-800">By <?= htmlspecialchars($blog_data['username']); ?></p>
            <p class="text-gray-400 text-xs">Published on: <?= date('F d, Y', strtotime($blog_data['publish_date'])); ?></p>
          </div>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-8">
          <p class="font-bold text-blue-950 text-xs uppercase tracking-wider mb-1">Quick Summary Description:</p>
          <p class="text-gray-700 italic text-base">
            <?= htmlspecialchars($blog_data['description']); ?>
          </p>
        </div>

        <div class="text-gray-800 text-lg leading-relaxed whitespace-pre-line font-serif">
          <?= htmlspecialchars($blog_data['content']); ?>
        </div>
      </article>
    <?php } else { ?>
      <div class="text-center py-20 bg-white m-10 rounded shadow max-w-md mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Article Not Found</h2>
        <p class="text-gray-500 mb-6">The article you requested could not be found or has been moved.</p>
        <a href="index.php?page=home" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition">Return to Home</a>
      </div>
    <?php } ?>

  <?php } ?>

  <footer class="text-center p-6 mt-10 bg-white">
    <p>© 2026 BlogHub</p>
  </footer>
  <script src="index.js"></script>

</body>

</html>
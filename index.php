<?php
include("admin.php");

// Fallback logic if the 'page' parameter isn't present in the URL query string
$page = $_GET['page'] ?? 'home';
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
      <a href="index.php?page=create" class="text-blue-600 font-medium hover:underline">Create Post</a>
    </div>
  </nav>

  <?php if ($page == 'home') { ?>
    <section class="bg-[url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRyPzfJ5ub8Yrp36q4Py4WIqiZ6x_Q4ftcRfHmlOAsTVj-BB6AsFTwHlUA&s=10')] bg-cover bg-center bg-gray-100 py-20 px-8 rounded-b shadow-md">
      <div class="container mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2 bg-white/75 backdrop-blur-sm p-6 rounded-lg shadow-sm">
          <h1 class="text-5xl font-bold text-gray-800 mb-4">
            Share Your Ideas With The World
          </h1>
          <p class="text-gray-600 text-lg mb-6">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam
            velit, rerum nobis recusandae sed iusto cumque, tempore odit.
          </p>
        </div>

        <div class="md:w-1/2 mt-10 md:mt-0">
          <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a" alt="Blog Hero" class="rounded-lg shadow-lg w-full" />
        </div>
      </div>
    </section>

    <div class="p-4 text-center mt-4">
      <form action="index.php" method="GET">
        <input type="hidden" name="page" value="search" />
        <input type="text" name="q" placeholder="Search blogs..." class="p-2 border rounded w-1/2 shadow-sm focus:outline-blue-500" />
      </form>
    </div>
  <?php } ?>

  <?php if ($page == 'home') { ?>

    <div class="p-10 bg-blue-100">
      <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Popular Blogs</h2>
          <a href="index.php?page=popular" class="text-blue-600 hover:underline font-medium">View More →</a>
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
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More →</a>
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
          <a href="index.php?page=all" class="text-blue-600 hover:underline font-medium">View All →</a>
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
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More →</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

  <?php } elseif ($page == 'popular') { ?>

    <div class="p-10 bg-blue-50">
      <div class="container mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Trending & Popular Blogs</h2>

        <?php
        // Fetches all popular blogs without page-limit constraints
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
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More →</a>
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
                  <a href="index.php?page=single&id=<?= $blog['blog_id']; ?>" class="text-blue-600 hover:underline font-semibold block">Read More →</a>
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
        <a href="index.php?page=home" class="text-blue-600 hover:underline inline-block mb-6 font-semibold">← Back to Homepage</a>

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

</body>

</html>
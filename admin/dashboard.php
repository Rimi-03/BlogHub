<?php
session_start();
include("../db.php");

if (!isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit();
}

// --- NEW AJAX ENDPOINT FOR FETCHING FULL BLOG CONTENT ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'ajax_get_blog_body') {
    header('Content-Type: application/json');
    $blog_id = intval($_POST['blog_id']);

    $stmt = $conn->prepare("SELECT blogs.*, author.username FROM blogs JOIN author ON blogs.author_id = author.author_id WHERE blogs.blog_id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $blog_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($blog_data) {
        $blog_data['publish_date_formatted'] = date('F d, Y', strtotime($blog_data['publish_date']));
        // Normalize cover image path
        if (strpos($blog_data['cover_image'], 'http') !== 0) {
            $blog_data['cover_image'] = '../' . $blog_data['cover_image'];
        }
        echo json_encode(['success' => true, 'blog' => $blog_data]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Blog story not found.']);
    }
    exit();
}

// --- AJAX ENDPOINT FOR CREATING AUTHOR WITHOUT REFRESHING ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'ajax_create_author') {
    header('Content-Type: application/json');
    $username = trim($_POST['username']);

    if (empty($username)) {
        echo json_encode(['success' => false, 'error' => 'Author name cannot be empty.']);
        exit();
    }

    $check = $conn->prepare("SELECT author_id FROM author WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Author username already exists!']);
    } else {
        $stmt = $conn->prepare("INSERT INTO author (username) VALUES (?)");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'New Author created successfully!',
                'author_id' => $conn->insert_id,
                'username' => $username
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create author.']);
        }
        $stmt->close();
    }
    $check->close();
    exit();
}

// --- POST/REDIRECT/GET ENGINE ---
$message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : "";
$error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : "";

unset($_SESSION['flash_message']);
unset($_SESSION['flash_error']);

// Handle Standard Form POST Actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // --- CREATE BLOG ---
    if ($_POST['action'] === 'create_blog') {
        $title = trim($_POST['title']);
        $subtitle = trim($_POST['subtitle']);
        $description = trim($_POST['description']);
        $content = trim($_POST['content']);
        $author_id = intval($_POST['author_id']);

        $cover_image = 'https://images.unsplash.com/photo-1455390582262-044cdead277a';
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['cover_image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['cover_image']['name']);
            $upload_dir = '../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                $cover_image = 'uploads/' . $file_name;
            }
        }

        $stmt = $conn->prepare("INSERT INTO blogs (title, subtitle, description, content, author_id, cover_image, publish_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssiss", $title, $subtitle, $description, $content, $author_id, $cover_image);

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Blog post created successfully!";
        } else {
            $_SESSION['flash_error'] = "Failed to create blog post.";
        }
        $stmt->close();

        header("Location: dashboard.php");
        exit();
    }

    // --- UPDATE BLOG ---
    if ($_POST['action'] === 'update_blog') {
        $blog_id = intval($_POST['blog_id']);
        $title = trim($_POST['title']);
        $subtitle = trim($_POST['subtitle']);
        $description = trim($_POST['description']);
        $content = trim($_POST['content']);
        $author_id = intval($_POST['author_id']);
        $cover_image = trim($_POST['existing_cover_image']);

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['cover_image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['cover_image']['name']);
            $upload_dir = '../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                $cover_image = 'uploads/' . $file_name;
            }
        }

        $stmt = $conn->prepare("UPDATE blogs SET title=?, subtitle=?, description=?, content=?, author_id=?, cover_image=? WHERE blog_id=?");
        $stmt->bind_param("ssssisi", $title, $subtitle, $description, $content, $author_id, $cover_image, $blog_id);

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Blog post updated successfully!";
        } else {
            $_SESSION['flash_error'] = "Failed to update blog post.";
        }
        $stmt->close();

        header("Location: dashboard.php");
        exit();
    }

    // --- DELETE BLOG ---
    if ($_POST['action'] === 'delete_blog') {
        $blog_id = intval($_POST['blog_id']);
        $stmt = $conn->prepare("DELETE FROM blogs WHERE blog_id = ?");
        $stmt->bind_param("i", $blog_id);
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Blog post deleted successfully!";
        } else {
            $_SESSION['flash_error'] = "Failed to delete blog post.";
        }
        $stmt->close();

        header("Location: dashboard.php");
        exit();
    }

    // --- DELETE AUTHOR PROFILE ---
    if ($_POST['action'] === 'delete_author') {
        $author_id = intval($_POST['author_id']);
        $stmt = $conn->prepare("DELETE FROM author WHERE author_id = ?");
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Author profile removed successfully!";
        } else {
            $_SESSION['flash_error'] = "Failed to delete author (check dependencies).";
        }
        $stmt->close();

        header("Location: dashboard.php");
        exit();
    }
}

// 3. Handle Active Author Filter via GET Parameter
$filter_author_id = isset($_GET['author_view_id']) ? intval($_GET['author_view_id']) : null;
$active_author_name = "";

if ($filter_author_id) {
    $auth_stmt = $conn->prepare("SELECT username FROM author WHERE author_id = ?");
    $auth_stmt->bind_param("i", $filter_author_id);
    $auth_stmt->execute();
    $auth_res = $auth_stmt->get_result()->fetch_assoc();
    if ($auth_res) {
        $active_author_name = $auth_res['username'];
    }
    $auth_stmt->close();
}

// 4. Fetch All Available Authors
$authors_res = $conn->query("SELECT * FROM author ORDER BY username ASC");
$authors = [];
while ($r = $authors_res->fetch_assoc()) {
    $authors[] = $r;
}

// 5. Fetch Blogs
if ($filter_author_id) {
    $blog_stmt = $conn->prepare("SELECT blogs.*, author.username FROM blogs JOIN author ON blogs.author_id = author.author_id WHERE blogs.author_id = ? ORDER BY blogs.publish_date DESC");
    $blog_stmt->bind_param("i", $filter_author_id);
    $blog_stmt->execute();
    $blogs_res = $blog_stmt->get_result();
} else {
    $blogs_res = $conn->query("SELECT blogs.*, author.username FROM blogs JOIN author ON blogs.author_id = author.author_id ORDER BY blogs.publish_date DESC");
}

// 6. Handle Target Blog for Editing view
$edit_blog = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_stmt = $conn->prepare("SELECT * FROM blogs WHERE blog_id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_blog = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}

$is_form_active = ($edit_blog);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BlogHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800 font-sans relative">

    <div id="toastContainer" class="fixed top-5 right-5 z-50 space-y-3 pointer-events-none max-w-sm w-full px-4 sm:px-0">
        <?php if ($message) { ?>
            <div class="toast-alert pointer-events-auto bg-green-600 text-white p-4 rounded-xl shadow-xl flex items-center justify-between font-medium text-sm transition-all duration-300">
                <span><?= htmlspecialchars($message) ?></span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 font-bold ml-3 text-lg">&times;</button>
            </div>
        <?php } ?>
        <?php if ($error) { ?>
            <div class="toast-alert pointer-events-auto bg-red-600 text-white p-4 rounded-xl shadow-xl flex items-center justify-between font-medium text-sm transition-all duration-300">
                <span><?= htmlspecialchars($error) ?></span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 font-bold ml-3 text-lg">&times;</button>
            </div>
        <?php } ?>
    </div>

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40 px-4 py-3 shadow-sm">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center justify-between w-full md:w-auto">
                <div class="flex items-center gap-2.5">
                    <a href="dashboard.php" class="text-xl font-bold tracking-tight text-blue-600 hover:text-blue-700">BlogHub Dashboard</a>
                    <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-1 rounded-md font-medium whitespace-nowrap">
                        Admin Mode: <strong class="text-blue-600"><?= htmlspecialchars($_SESSION["admin_name"] ?? 'Active') ?></strong>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-3 sm:flex items-center justify-center md:justify-end gap-2 w-full md:w-auto border-t md:border-0 pt-2 md:pt-0">
                <button onclick="toggleCreatePostForm()" class="text-[11px] sm:text-sm bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition shadow-sm truncate text-center">
                    <?= $is_form_active ? 'Show Posts' : 'Create Post' ?>
                </button>
                <button onclick="toggleAuthorManagementModal()" class="text-[11px] sm:text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition shadow-sm truncate text-center">
                    Create Author
                </button>
                <a href="index.php?action=logout" class="text-[11px] sm:text-sm text-center text-red-500 hover:text-red-600 border border-red-200 hover:bg-red-50 py-2 px-3 sm:px-4 rounded-lg transition font-medium truncate">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

        <?php if ($filter_author_id) { ?>
            <div class="mb-6">
                <button onclick="toggleAuthorManagementModal()"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 
                       bg-blue-50 hover:bg-blue-100 px-4 py-2.5 rounded-xl transition border border-blue-100 shadow-sm"
                    aria-label="Open Author Management Hub">
                    <svg xmlns="http://www.w3.org/2000/xl" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
            </div>
        <?php } ?>

        <div id="createPostSection" class="<?= $is_form_active ? 'block' : 'hidden' ?> bg-white p-5 sm:p-6 rounded-2xl border border-gray-200 shadow-sm mb-8 transition-all duration-300">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h2 class="text-lg font-bold text-gray-900">
                    <?= $edit_blog ? 'Update This Post' : 'Create a Brand New Blog Story' ?>
                </h2>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleCreatePostForm()" class="text-gray-400 hover:text-gray-600 text-2xl font-medium leading-none">&times;</button>
                </div>
            </div>

            <form method="POST" action="dashboard.php" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="<?= $edit_blog ? 'update_blog' : 'create_blog' ?>">
                <?php if ($edit_blog): ?>
                    <input type="hidden" name="blog_id" value="<?= $edit_blog['blog_id'] ?>">
                    <input type="hidden" name="existing_cover_image" value="<?= htmlspecialchars($edit_blog['cover_image']) ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Title *</label>
                        <input type="text" name="title" required value="<?= $edit_blog ? htmlspecialchars($edit_blog['title']) : '' ?>" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" value="<?= $edit_blog ? htmlspecialchars($edit_blog['subtitle']) : '' ?>" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Assign Author *</label>
                        <select name="author_id" id="authorSelectField" required onchange="checkInlineAuthorSelection(this)" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                            <option value="">-- Choose Author --</option>
                            <option value="NEW_MODAL_TRIGGER" class="text-blue-600 font-bold bg-blue-50">Create New Author</option>
                            <?php foreach ($authors as $auth) { ?>
                                <option value="<?= $auth['author_id'] ?>" <?= ($edit_blog && $edit_blog['author_id'] == $auth['author_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($auth['username']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Upload Cover Image File</label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full border border-gray-300 rounded-xl p-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Short Description *</label>
                    <input type="text" name="description" required value="<?= $edit_blog ? htmlspecialchars($edit_blog['description']) : '' ?>" class="w-full border border-gray-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Summary snippet...">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Content Body *</label>
                    <textarea name="content" rows="5" required class="w-full border border-gray-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none font-serif" placeholder="Write content..."><?= $edit_blog ? htmlspecialchars($edit_blog['content']) : '' ?></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="toggleCreatePostForm()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-xl transition">View Recent Posts</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-5 py-2.5 rounded-xl transition shadow-sm">
                        <?= $edit_blog ? 'Save Changes' : 'Publish Article' ?>
                    </button>
                </div>
            </form>
        </div>

        <div id="recentPostsSection" class="<?= $is_form_active ? 'hidden' : 'block' ?> space-y-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <?= $filter_author_id ? "Showing Stories by " . htmlspecialchars($active_author_name) : "All System Blog Stories" ?>
                    <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full font-normal"><?= $blogs_res->num_rows ?> articles</span>
                </h3>
            </div>

            <div class="space-y-3">
                <?php if ($blogs_res->num_rows > 0) {
                    while ($blog = $blogs_res->fetch_assoc()) {
                        $imgUrl = (strpos($blog['cover_image'], 'http') === 0) ? $blog['cover_image'] : '../' . $blog['cover_image'];
                ?>
                        <div onclick="openBlogReadModal(<?= $blog['blog_id'] ?>)"
                            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between shadow-sm hover:border-gray-300 transition cursor-pointer group/row">

                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <img src="<?= htmlspecialchars($imgUrl) ?>" class="w-12 h-12 rounded-lg object-cover bg-gray-100 flex-shrink-0 border">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-gray-900 text-sm line-clamp-1 leading-snug group-hover/row:text-blue-600 transition"><?= htmlspecialchars($blog['title']) ?></h4>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-1 flex-wrap">
                                        <span>By <strong class="text-blue-600 font-medium"><?= htmlspecialchars($blog['username']) ?></strong></span>
                                        <span>•</span>
                                        <span><?= date('M d, Y', strtotime($blog['publish_date'])) ?></span>
                                        <span class="bg-blue-50 border border-blue-200 text-blue-700 px-1.5 py-0.2 rounded text-[10px] font-bold">
                                            <?= number_format($blog['views']) ?> Views
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end border-t sm:border-0 pt-2 sm:pt-0 shrink-0">
                                <button onclick="event.stopPropagation(); window.location.href='dashboard.php?edit_id=<?= $blog['blog_id'] ?><?= $filter_author_id ? '&author_view_id=' . $filter_author_id : '' ?>'"
                                    class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-1.5 border border-gray-200 rounded-lg transition font-medium">
                                    Edit
                                </button>
                                <form method="POST" action="dashboard.php" onsubmit="event.stopPropagation(); return confirm('Are you sure you want to permanently delete this blog story?');" class="inline">
                                    <input type="hidden" name="action" value="delete_blog">
                                    <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
                                    <button type="submit" onclick="event.stopPropagation();"
                                        class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 border border-red-100 rounded-lg transition font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="text-center p-8 bg-white border rounded-xl text-gray-400 text-sm italic">No blog stories found.</div>
                <?php } ?>
            </div>
        </div>
    </main>

    <div id="blogReadModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50 transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-5 sm:p-6 shadow-xl border border-gray-100 transform scale-95 transition-transform duration-300 max-h-[85vh] flex flex-col" id="blogReadCard">
            <div class="flex justify-between items-center mb-4 border-b pb-2 shrink-0">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Article Quick Preview</h3>
                <button type="button" onclick="closeBlogReadModal()" class="text-gray-400 hover:text-gray-600 text-xl font-medium">&times;</button>
            </div>

            <div class="overflow-y-auto space-y-4 pr-1 flex-1" id="blogReadContent">
            </div>
        </div>
    </div>

    <div id="authorManagementModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm hidden items-center justify-center p-4 z-50 transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-5 sm:p-6 shadow-xl border border-gray-100 transform scale-95 transition-transform duration-300" id="authorModalCard">

            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">Author Management Hub</h3>

                <button type="button"
                    onclick="<?= $filter_author_id ? "window.location.href='dashboard.php'" : "toggleAuthorManagementModal()" ?>"
                    class="text-gray-400 hover:text-gray-600 text-xl font-medium">&times;</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Create New Author</h4>
                    <form id="ajaxAuthorForm" onsubmit="submitAuthorFormAsync(event)">
                        <input type="hidden" id="modalOriginSource" value="navbar_button">

                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Author Name *</label>
                            <input type="text" id="modalAuthorNameInput" required placeholder="e.g., Rachel Green"
                                class="w-full border border-gray-300 rounded-xl p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-800 bg-white">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 rounded-xl transition shadow-sm">
                            Save Profile
                        </button>
                    </form>
                </div>

                <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Available Authors</h4>
                    <p class="text-[11px] text-gray-400 mb-3">Click any name to display their posts summary stack on the dashboard.</p>

                    <div id="modalAuthorsListView" class="divide-y divide-gray-100 max-h-[220px] overflow-y-auto border border-gray-200 rounded-xl bg-white p-2">
                        <?php foreach ($authors as $auth) { ?>
                            <div class="flex items-center justify-between py-2.5 px-3 hover:bg-gray-50 rounded-lg group transition" id="author_row_<?= $auth['author_id'] ?>">
                                <a href="dashboard.php?author_view_id=<?= $auth['author_id'] ?>" class="text-xs font-semibold text-gray-700 hover:text-blue-600 transition truncate pr-2">
                                    <?= htmlspecialchars($auth['username']) ?>
                                </a>

                                <form method="POST" action="dashboard.php" onsubmit="return confirm('Remove author profile completely?');">
                                    <input type="hidden" name="action" value="delete_author">
                                    <input type="hidden" name="author_id" value="<?= $auth['author_id'] ?>">
                                    <button type="submit" class="text-[10px] text-red-500 hover:text-red-700 font-medium opacity-60 group-hover:opacity-100 transition shrink-0">Remove</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 mt-4 border-t">
                <button type="button"
                    onclick="<?= $filter_author_id ? "window.location.href='dashboard.php'" : "toggleAuthorManagementModal()" ?>"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-4 py-2 rounded-xl transition">Close</button>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>

</html>

<!-- 
multiple image
 popular(on view)
 session(admin authentication)
 dynamic -->
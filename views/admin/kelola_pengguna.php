<?php
/**
 * File: views/admin/kelola_pengguna.php
 * Deskripsi: Halaman admin untuk mengelola pengguna atau admin.
 */

// Sertakan file koneksi database dan controller yang diperlukan
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: /variasi-motor/admin/login");
    exit();
}

// Inisialisasi controller
$userController = new UserController($conn);
$adminController = new AdminController($conn);

// Tentukan apakah kita mengelola 'pengguna' atau 'admin' berdasarkan URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));
$manage_type = end($path_parts) == 'kelola_admin' ? 'admin' : 'pengguna';

$data_list = [];
if ($manage_type === 'admin') {
    $data_list = $adminController->getAllAdmins();
} else {
    $data_list = $userController->getAllUsers();
}

// Sertakan template header
require_once __DIR__ . '/templates/header.php';
?>

    <div class="flex">
        <!-- Sidebar Admin -->
        <?php require_once __DIR__ . '/templates/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 bg-slate-50 text-slate-800">
            <header class="flex justify-between items-center pb-4 border-b border-slate-200 mb-6">
                <h1 class="text-3xl font-bold">Kelola Pengguna</h1>
                <p class="text-slate-500">Selamat datang, <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>!</p>
            </header>

            <?php
            // Tampilkan pesan sukses atau error jika ada
            if (isset($_SESSION['success_message'])) {
                echo '<div class="bg-green-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="bg-red-500 text-white p-3 rounded-lg text-sm mb-4">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <!-- Tabs Navigasi -->
            <div class="bg-white p-4 rounded-t-xl shadow-xl mb-4">
                <div class="flex space-x-4">
                    <a href="/variasi-motor/admin/kelola_pengguna" class="tab-button px-4 py-2 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-200 <?php echo $manage_type === 'pengguna' ? 'bg-blue-500 text-white' : 'text-slate-500 hover:bg-slate-100'; ?>">
                        Kelola Pengguna
                    </a>
                    <a href="/variasi-motor/admin/kelola_admin" class="tab-button px-4 py-2 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-200 <?php echo $manage_type === 'admin' ? 'bg-blue-500 text-white' : 'text-slate-500 hover:bg-slate-100'; ?>">
                        Kelola Admin
                    </a>
                </div>
            </div>

            <!-- Form Tambah Admin (hanya muncul di tab Admin) -->
            <?php if ($manage_type === 'admin'): ?>
            <div id="tambah-admin-form" class="bg-white p-6 rounded-b-xl shadow-xl mb-8">
                <h2 class="text-2xl font-bold mb-6 text-slate-800">Tambah Admin Baru</h2>
                <form action="/variasi-motor/admin/kelola_admin/tambah" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <label for="username" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Username
                        </label>
                        <input type="text" id="username" name="username" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <div>
                        <label for="password" class="block text-slate-500 font-semibold mb-2 flex items-center">
                            <i class="fas fa-lock mr-2"></i> Password
                        </label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <div class="md:col-span-2 mt-4">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> Tambah Admin
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Daftar Pengguna/Admin -->
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <h2 class="text-2xl font-bold mb-4">Daftar <?php echo ucfirst($manage_type); ?></h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID <?php echo ucfirst($manage_type); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Dibuat</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php if (empty($data_list)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-slate-500">Belum ada <?php echo $manage_type; ?>.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data_list as $item): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800"><?php echo htmlspecialchars($item['id']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($item['username']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800"><?php echo htmlspecialchars($item['created_at']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <!-- Tombol Hapus -->
                                            <form action="/variasi-motor/admin/kelola_<?php echo $manage_type; ?>/hapus" method="POST" class="inline-block">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-700 mx-2 transition-colors duration-200" onclick="return confirm('Apakah Anda yakin ingin menghapus <?php echo $manage_type; ?> ini?');">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<?php
// Sertakan template footer
require_once __DIR__ . '/templates/footer.php';
?>

<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$row = ['media_type' => 'photo', 'title' => '', 'description' => '', 'media_path' => '', 'thumbnail_path' => '', 'product_id' => null, 'is_active' => 1, 'sort_order' => 0];
if ($id) {
    $q = $pdo->prepare('SELECT * FROM gallery_media WHERE id=?');
    $q->execute([$id]);
    $row = $q->fetch() ?: $row;
}
$error = '';

function media_file_size_limit(string $type, int $fileCount): int
{
    return ($type === 'video' ? ($fileCount > 1 ? 15 : 3) : ($fileCount > 1 ? 10 : 2)) * 1024 * 1024;
}

function compress_image(string $source, int $limit): string
{
    $info = getimagesize($source);
    if (!$info) throw new RuntimeException('Invalid image file.');
    $image = match ($info['mime']) {
        'image/jpeg' => imagecreatefromjpeg($source),
        'image/png' => imagecreatefrompng($source),
        'image/webp' => imagecreatefromwebp($source),
        default => false,
    };
    if (!$image) throw new RuntimeException('Unsupported image format.');

    $width = imagesx($image);
    $height = imagesy($image);
    $scale = min(1, 2400 / max($width, $height));
    $outputWidth = max(1, (int) round($width * $scale));
    $outputHeight = max(1, (int) round($height * $scale));
    $resized = imagecreatetruecolor($outputWidth, $outputHeight);
    imagecopyresampled($resized, $image, 0, 0, 0, 0, $outputWidth, $outputHeight, $width, $height);
    $temporary = tempnam(sys_get_temp_dir(), 'benaki-image-') . '.webp';
    for ($quality = 82; $quality >= 35; $quality -= 7) {
        imagewebp($resized, $temporary, $quality);
        if (filesize($temporary) <= $limit) {
            imagedestroy($image);
            imagedestroy($resized);
            return $temporary;
        }
    }
    imagedestroy($image);
    imagedestroy($resized);
    @unlink($temporary);
    throw new RuntimeException('Image could not be reduced below the upload limit.');
}

function compress_video(string $source, int $limit): string
{
    global $config;
    $ffmpeg = (string) ($config['app']['ffmpeg_bin'] ?? 'ffmpeg');
    $temporary = tempnam(sys_get_temp_dir(), 'benaki-video-') . '.mp4';
    for ($crf = 28; $crf <= 42; $crf += 4) {
        $command = sprintf('%s -y -i %s -c:v libx264 -preset medium -crf %d -c:a aac -b:a 64k -movflags +faststart %s 2>&1', escapeshellarg($ffmpeg), escapeshellarg($source), $crf, escapeshellarg($temporary));
        exec($command, $output, $status);
        if ($status === 0 && is_file($temporary) && filesize($temporary) <= $limit) return $temporary;
        @unlink($temporary);
    }
    throw new RuntimeException('Video could not be reduced below the upload limit.');
}

function upload_media_file(string $field, string $type, ?int $index = null, ?int $limit = null): ?string
{
    global $config;
    if (empty($_FILES[$field])) return null;
    $file = $_FILES[$field];
    if ($index !== null) {
        if (!isset($file['error'][$index])) return null;
        $file = ['error' => $file['error'][$index], 'size' => $file['size'][$index], 'tmp_name' => $file['tmp_name'][$index]];
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload failed.');
    if ($file['size'] > ($config['app']['upload_max_bytes'] ?? 15 * 1024 * 1024)) throw new RuntimeException('File exceeds the 15MB server upload limit.');
    $tmp = $file['tmp_name'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $allowed = $type === 'photo' ? ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'] : ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/ogg' => 'ogv'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Unsupported file format.');
    $limit ??= media_file_size_limit($type, 1);
    $processed = $tmp;
    $extension = $allowed[$mime];
    if ((int) $file['size'] > $limit) {
        $processed = $type === 'video' ? compress_video($tmp, $limit) : compress_image($tmp, $limit);
        $extension = $type === 'video' ? 'mp4' : 'webp';
    }
    $name = bin2hex(random_bytes(18)) . '.' . $extension;
    $relative = 'assets/uploads/media/' . $name;
    $destination = dirname(__DIR__) . '/' . $relative;
    if (!is_dir(dirname($destination))) mkdir(dirname($destination), 0755, true);
    if ($processed === $tmp) {
        if (!move_uploaded_file($tmp, $destination)) throw new RuntimeException('Could not save upload.');
    } elseif (!rename($processed, $destination)) {
        @unlink($processed);
        throw new RuntimeException('Could not save compressed upload.');
    }
    return $relative;
}

if (is_post()) {
    verify_csrf($_POST['csrf'] ?? '');
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $type = ($_POST['media_type'] ?? 'photo') === 'video' ? 'video' : 'photo';
    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: null;
    $active = isset($_POST['is_active']) ? 1 : 0;
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    if ($title === '') {
        $error = 'Title is required.';
    } else {
        try {
            $old = $id ? (function () use ($pdo, $id) { $q = $pdo->prepare('SELECT * FROM gallery_media WHERE id=?'); $q->execute([$id]); return $q->fetch(); })() : null;
            $thumbnail = upload_media_file('thumbnail', 'photo') ?? ($old['thumbnail_path'] ?? null);
            $mediaFiles = [];
            if ($id) {
                $media = upload_media_file('media', $type, null, media_file_size_limit($type, 1)) ?? ($old['media_path'] ?? null);
                if ($media) $mediaFiles[] = $media;
            } else {
                $selectedFiles = array_filter($_FILES['media']['error'] ?? [], static fn (int $error): bool => $error !== UPLOAD_ERR_NO_FILE);
                $fileCount = max(1, count($selectedFiles));
                $perFileLimit = intdiv(media_file_size_limit($type, $fileCount), $fileCount);
                foreach (array_keys($_FILES['media']['name'] ?? []) as $index) {
                    $media = upload_media_file('media', $type, (int) $index, $perFileLimit);
                    if ($media) $mediaFiles[] = $media;
                }
            }
            if (!$mediaFiles) {
                $error = 'Please upload a media file.';
            } elseif ($id) {
                $q = $pdo->prepare('UPDATE gallery_media SET media_type=?,title=?,description=?,media_path=?,thumbnail_path=?,product_id=?,is_active=?,sort_order=? WHERE id=?');
                $q->execute([$type, $title, $description ?: null, $mediaFiles[0], $thumbnail, $productId, $active, $sortOrder, $id]);
                flash('success', 'Gallery media saved.');
                redirect_to('media.php');
            } else {
                $q = $pdo->prepare('INSERT INTO gallery_media(media_type,title,description,media_path,thumbnail_path,product_id,is_active,sort_order) VALUES(?,?,?,?,?,?,?,?)');
                foreach ($mediaFiles as $media) $q->execute([$type, $title, $description ?: null, $media, $thumbnail, $productId, $active, $sortOrder]);
                flash('success', count($mediaFiles) > 1 ? 'Gallery media files saved.' : 'Gallery media saved.');
                redirect_to('media.php');
            }
        } catch (Throwable $exception) { $error = $exception->getMessage(); }
    }
}

$products = $pdo->query('SELECT id,name FROM products ORDER BY sort_order,id')->fetchAll();
$pageTitle = $id ? 'Edit Media' : 'Upload Media';
$active = 'media';
require __DIR__ . '/includes/layout.php';
if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
<div class="panel form-card"><form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $id ?>">
    <div class="grid2"><div class="field"><label>Media type</label><select name="media_type" id="mediaType"><option value="photo" <?= $row['media_type'] === 'photo' ? 'selected' : '' ?>>Photo</option><option value="video" <?= $row['media_type'] === 'video' ? 'selected' : '' ?>>Video</option></select></div><div class="field"><label>Related fabric</label><select name="product_id"><option value="">General store media</option><?php foreach ($products as $product): ?><option value="<?= $product['id'] ?>" <?= $row['product_id'] == $product['id'] ? 'selected' : '' ?>><?= e($product['name']) ?></option><?php endforeach; ?></select></div></div>
    <div class="field"><label>Title *</label><input name="title" value="<?= e($row['title']) ?>" required></div><div class="field"><label>Description</label><textarea name="description" rows="3"><?= e($row['description']) ?></textarea></div>
    <div class="grid2"><div class="field"><label>Media file<?= $id ? '' : 's' ?> *</label><input name="media<?= $id ? '' : '[]' ?>" type="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg" <?= $id ? '' : 'multiple' ?>><small>Files are automatically reduced to 3MB video / 2MB image for one file, or 15MB video / 10MB images in total for multiple files.</small></div><div class="field"><label>Video thumbnail (optional)</label><input name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp"></div></div>
    <div class="grid2"><div class="field"><label>Display order</label><input name="sort_order" type="number" value="<?= e((string) $row['sort_order']) ?>"></div><div class="field"><label><input type="checkbox" name="is_active" <?= $row['is_active'] ? 'checked' : '' ?>> Publish</label></div></div>
    <button class="btn btn-gold">Save Media</button> <a class="btn btn-light" href="media.php">Cancel</a>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
declare(strict_types=1);
function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money(float $v): string { return '₦'.number_format($v, 0); }
function slugify(string $v): string { $v=strtolower(trim($v)); $v=preg_replace('/[^a-z0-9]+/','-',$v)??''; return trim($v,'-') ?: 'item-'.time(); }
function base_url(string $path=''): string {
    $cfg=$GLOBALS['config']??[];
    $base=rtrim((string)($cfg['app']['base_url']??''),'/');
    if($base===''){
        $script=(string)($_SERVER['SCRIPT_NAME']??'');
        $root=dirname($script);
        if(in_array(basename(str_replace('\\','/',$root)), ['admin','ajax','payment'], true)) $root=dirname($root);
        $base=($root==='.'||$root==='\\')?'':rtrim($root,'/');
    }
    return $base.'/'.ltrim($path,'/');
}
function site_url(string $url=''): string {
    $url=trim($url);
    if($url==='') return base_url('index.php');
    if(preg_match('~^(https?:)?//|^(mailto:|tel:|#)~i',$url)) return $url;
    return base_url($url);
}
function absolute_site_url(string $url=''): string {
    $target = site_url($url);
    if (preg_match('~^https?://~i', $target)) return $target;
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = trim((string)($_SERVER['HTTP_HOST'] ?? ''));
    return $host === '' ? $target : $scheme . '://' . $host . '/' . ltrim($target, '/');
}
function media_url(?string $path): string { if(!$path) return base_url('assets/img/fabric-stack.jpg'); return base_url($path); }
function json_list(?string $v): array { if(!$v) return []; $a=json_decode($v,true); return is_array($a)?$a:[]; }
function color_swatch(string $color): string {
    $swatches = [
        'white' => '#ffffff', 'cream' => '#f5e6c8', 'gold' => '#d4a017', 'yellow' => '#facc15',
        'pink' => '#ec4899', 'purple' => '#8b5cf6', 'wine' => '#722f37', 'burgundy' => '#800020',
        'red' => '#dc2626', 'blue' => '#2563eb', 'navy' => '#172554', 'green' => '#16a34a',
        'teal' => '#0f766e', 'black' => '#111827', 'grey' => '#6b7280', 'gray' => '#6b7280',
        'brown' => '#92400e',
    ];
    return $swatches[strtolower(trim($color))] ?? '#cbd5e1';
}
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function verify_csrf(string $token): void { if(!hash_equals((string)($_SESSION['csrf']??''),$token)) { http_response_code(419); exit('Invalid security token.'); } }
function is_post(): bool { return ($_SERVER['REQUEST_METHOD']??'GET')==='POST'; }
function redirect_to(string $url): never { header('Location: '.$url); exit; }

function cart_items(): array {
    $cart = is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];
    $normalized = [];
    $changed = false;
    foreach ($cart as $storedKey => $item) {
        $colors = is_array($item['colors'] ?? null) ? array_values(array_unique(array_filter(array_map('trim', $item['colors'])))) : [];
        if (count($colors) > 1) {
            $changed = true;
            foreach ($colors as $color) {
                $copy = $item;
                $copy['colors'] = [$color];
                $copy['key'] = cart_key((int)$copy['product_id'], (string)$copy['measurement'], $color);
                $normalized[$copy['key']] = $copy;
            }
        } else {
            $copy = $item;
            $copy['colors'] = $colors;
            if (count($colors) === 1) $copy['key'] = cart_key((int)$copy['product_id'], (string)$copy['measurement'], $colors[0]);
            $normalized[$copy['key'] ?? $storedKey] = $copy;
        }
    }
    if ($changed) $_SESSION['cart'] = $normalized;
    return $normalized;
}
function cart_count(): int { return count(cart_items()); }
function cart_key(int $productId, string $measurement, string $color): string {
    return sha1($productId.'|'.$measurement.'|'.strtolower(trim($color)));
}
function anniversary_discount(float $quantity, float $unitPrice = 0): float {
    if ($unitPrice >= 4000) {
        if ($quantity > 30) return 4500;
        if ($quantity >= 20) return 3000;
        if ($quantity >= 10) return 2000;
        if ($quantity >= 5) return 1500;
        if ($quantity >= 3) return 1000;
        return 0;
    }
    if ($unitPrice >= 3000) {
        if ($quantity > 30) return 3500;
        if ($quantity >= 20) return 2500;
        if ($quantity >= 10) return 2000;
        if ($quantity >= 5) return 1500;
        if ($quantity >= 3) return 500;
        return 0;
    }
    if ($quantity > 30) return 3000;
    if ($quantity >= 20) return 2000;
    if ($quantity >= 10) return 1500;
    if ($quantity >= 5) return 1000;
    if ($quantity >= 3) return 500;
    return 0;
}
function cart_total_quantity(array $cart): float {
    $quantity = 0.0;
    foreach ($cart as $item) {
        $quantity += (float)($item['quantity'] ?? 0);
    }
    return $quantity;
}

function cart_discount(array $cart): float {
    $discount = 0.0;
    foreach ($cart as $item) {
        $discount += anniversary_discount((float)($item['quantity'] ?? 0), (float)($item['unit_price'] ?? 0));
    }
    return $discount;
}
function paystack_config(): array { return $GLOBALS['config']['paystack'] ?? []; }

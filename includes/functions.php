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
        if(str_ends_with($root,'/admin')) $root=dirname($root);
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
function media_url(?string $path): string { if(!$path) return base_url('assets/img/fabric-stack.jpg'); return base_url($path); }
function json_list(?string $v): array { if(!$v) return []; $a=json_decode($v,true); return is_array($a)?$a:[]; }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function verify_csrf(string $token): void { if(!hash_equals((string)($_SESSION['csrf']??''),$token)) { http_response_code(419); exit('Invalid security token.'); } }
function is_post(): bool { return ($_SERVER['REQUEST_METHOD']??'GET')==='POST'; }
function redirect_to(string $url): never { header('Location: '.$url); exit; }

<?php
/**
 * Plugin Name: Carcaj Cloudflare IP
 * Description: Usa la IP real del visitante (CF-Connecting-IP) cuando la peticion viene de Cloudflare.
 *
 * Sin esto, Loginizer/Sucuri/comentarios ven la IP de Cloudflare y un bloqueo
 * por intentos fallidos dejaria afuera a todos. Solo se confia en el header si
 * REMOTE_ADDR pertenece a los rangos de Cloudflare (https://www.cloudflare.com/ips/,
 * revisados 2026-11-16).
 */

if (!defined('ABSPATH') || empty($_SERVER['HTTP_CF_CONNECTING_IP']) || empty($_SERVER['REMOTE_ADDR'])) {
	return;
}

function carcaj_ip_in_cidr(string $ip, string $cidr): bool {
	[$net, $bits] = explode('/', $cidr);
	$ip_bin  = @inet_pton($ip);
	$net_bin = @inet_pton($net);
	if ($ip_bin === false || $net_bin === false || strlen($ip_bin) !== strlen($net_bin)) {
		return false;
	}
	$bits  = (int) $bits;
	$bytes = intdiv($bits, 8);
	if (substr($ip_bin, 0, $bytes) !== substr($net_bin, 0, $bytes)) {
		return false;
	}
	$rem = $bits % 8;
	if ($rem === 0) {
		return true;
	}
	$mask = chr((0xff << (8 - $rem)) & 0xff);
	return ((ord($ip_bin[$bytes]) & ord($mask)) === (ord($net_bin[$bytes]) & ord($mask)));
}

(function () {
	$ranges = [
		'173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
		'141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
		'197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
		'104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
		'2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
		'2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
	];
	$remote = $_SERVER['REMOTE_ADDR'];
	foreach ($ranges as $cidr) {
		if (carcaj_ip_in_cidr($remote, $cidr)) {
			$real = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
			if (filter_var($real, FILTER_VALIDATE_IP)) {
				$_SERVER['HTTP_X_CARCAJ_CF_EDGE'] = $remote;
				$_SERVER['REMOTE_ADDR']           = $real;
			}
			return;
		}
	}
})();

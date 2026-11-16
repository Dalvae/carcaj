// Sirve carcaj.cl/wp-content/uploads/AAAA/MM/* desde el bucket R2 carcaj-media,
// en el mismo dominio del sitio (evita la conexion extra a media.carcaj.cl,
// que sumaba ~1,3 s al LCP movil en las entradas).
// Si el archivo no esta en R2, pasa al hosting (fetch(request)).
const RE = /^\/wp-content\/uploads\/(\d{4}\/\d{2}\/.+)$/;

export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);
    const m = url.pathname.match(RE);
    if (!m || (request.method !== 'GET' && request.method !== 'HEAD')) {
      return fetch(request);
    }

    const cache = caches.default;
    const cacheKey = new Request(url.origin + url.pathname, { method: 'GET' });
    let res = await cache.match(cacheKey);
    if (res) return request.method === 'HEAD' ? new Response(null, res) : res;

    const obj = await env.MEDIA.get(decodeURIComponent(m[1]));
    if (!obj) return fetch(request);

    const headers = new Headers();
    obj.writeHttpMetadata(headers);
    headers.set('etag', obj.httpEtag);
    headers.set('cache-control', 'public, max-age=31536000, immutable');
    headers.set('x-carcaj-source', 'r2');
    res = new Response(obj.body, { headers });
    ctx.waitUntil(cache.put(cacheKey, res.clone()));
    return request.method === 'HEAD' ? new Response(null, res) : res;
  },
};

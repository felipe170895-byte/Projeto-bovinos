(function () {
  const tokenMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

  if (csrfToken) {
    document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (form) {
      if (!form.querySelector('input[name="csrf_token"]')) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'csrf_token';
        input.value = csrfToken;
        form.appendChild(input);
      }
    });
  }

  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/service-worker.js').catch(function () {
        // Falha silenciosa para ambiente local sem HTTPS/host adequado.
      });
    });
  }
})();

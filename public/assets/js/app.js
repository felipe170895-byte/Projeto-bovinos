if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('/service-worker.js').catch(function () {
      // Falha silenciosa para ambiente local sem HTTPS/host adequado.
    });
  });
}

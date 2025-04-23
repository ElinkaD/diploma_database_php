window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('page');

    if (currentTab === 'import') {
        const script = document.createElement('script');
        script.src = './js/import.js';
        document.body.appendChild(script);
    }
});

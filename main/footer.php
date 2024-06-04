        <footer>
            <p> © 2024 Thales Alenia Space </p>
            <p> | </p>
            <p> © 2024 Université Nice Côte-d'Azur - IUT Réseaux et Télécommunications - Sophia Antipolis</p>
            <p> | </p>
            <p> © 2024 CHECKLIST - Groupe 6</p>
        </footer>
    </body>
</html>

<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        positionFooter();
    });

    window.addEventListener('resize', () => {
        positionFooter();
    });

    function positionFooter() {
        const body = document.body;
        const html = document.documentElement;
        const footer = document.querySelector('footer');

        const height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);

        if (height > window.innerHeight) {
            footer.style.position = 'relative';
        } else {
            footer.style.position = 'fixed';
            footer.style.bottom = '0';
        }
    }
</script>
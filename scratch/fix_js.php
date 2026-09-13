<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

// Remove the inline onclick handlers since they don't work due to scope
$content = str_replace(' onclick="closeMenu()"', '', $content);

// Update the script block
$scriptSearch = <<<JS
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const closeBtn = document.getElementById('close-menu-btn');
            const slideMenu = document.getElementById('slide-menu');
            const overlay = document.getElementById('menu-overlay');

            function openMenu() {
                slideMenu.classList.remove('slide-menu-closed');
                slideMenu.classList.add('slide-menu-open');
                overlay.classList.remove('hidden');
            }

            function closeMenu() {
                slideMenu.classList.remove('slide-menu-open');
                slideMenu.classList.add('slide-menu-closed');
                overlay.classList.add('hidden');
            }

            menuBtn.addEventListener('click', openMenu);
            closeBtn.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);
        });
JS;

$scriptReplace = <<<JS
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const closeBtn = document.getElementById('close-menu-btn');
            const slideMenu = document.getElementById('slide-menu');
            const overlay = document.getElementById('menu-overlay');

            function openMenu() {
                slideMenu.classList.remove('slide-menu-closed');
                slideMenu.classList.add('slide-menu-open');
                overlay.classList.remove('hidden');
            }

            function closeMenu() {
                slideMenu.classList.remove('slide-menu-open');
                slideMenu.classList.add('slide-menu-closed');
                overlay.classList.add('hidden');
            }

            menuBtn.addEventListener('click', openMenu);
            closeBtn.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);

            // Also close menu when clicking any link inside it
            const navLinks = slideMenu.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', closeMenu);
            });
        });
JS;

$content = str_replace($scriptSearch, $scriptReplace, $content);
file_put_contents($file, $content);
echo "Fixed JS scoping issue\n";


import './bootstrap.js';

import 'bootstrap';
import './styles/app.scss';

// Ajout du pading au body par rapport à la taille de la navbar fixed
const navbar = document.querySelector('.navbar.fixed-top');
if (navbar) {
    document.body.style.paddingTop = (navbar.offsetHeight + 20) + 'px';
}



// Permet d'eviter la double redirection si l'utilisateur double clique sur le lien
const linkDisabledOnClick = Array.from(document.getElementsByClassName('link-disabled-onclick'));
linkDisabledOnClick.forEach(element => {
    element.addEventListener('click', () => {
        element.classList.add('disabled');
    });
});



// Permet d'eviter le double submit si l'utilisateur double clique sur le bouton
const formPreventDoubleSubmission = Array.from(document.getElementsByClassName('form-prevent-double-submission'));
formPreventDoubleSubmission.forEach(element => {
    element.addEventListener('submit', (e) => {
        if (element.getAttribute('data-submitted')) {
            e.preventDefault();
        } else {
            element.setAttribute('data-submitted', 'true');
        }
    });
});

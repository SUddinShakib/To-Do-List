var form = document.getElementById('form');

form.addEventListener('mousemove', (e) => {
    var x = (window.innerWidth / 2 - e.pageX) / 20;
    var y = (window.innerHeight / 2 - e.pageY) / 20;

    form.style.transform = 'rotateX(' + x + 'deg) rotateY(' + y + 'deg)'
});
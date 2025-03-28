const toggleContent = (id) => {
    const box = document.getElementById(id);
    if (box.style.display === 'none')
        box.style.display = 'table-row';
    else
        box.style.display = 'none';
};
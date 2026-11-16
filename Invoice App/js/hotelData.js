function getHotelOptionsHTML(selectedHotel = "") {
    return (selectedHotel ? '' : '<option value="" selected>-- Hotel --</option>') +
        HOTELS.map(h => `<option value="${h}" ${h === selectedHotel ? "selected" : ""}>${h}</option>`).join('');
}
function updateUnitDatalist(hotelSelect) {
    const unitInput = hotelSelect.closest('tr').querySelector('.unit-number');
    const id = 'unit-options-' + Math.random().toString(36).substr(2, 5);
    const dl = document.createElement('datalist');
    dl.id = id;
    dl.innerHTML = (hotelUnitMap[hotelSelect.value] || []).map(u => `<option value="${u}">`).join("");
    document.body.appendChild(dl);
    unitInput.setAttribute('list', id);
}
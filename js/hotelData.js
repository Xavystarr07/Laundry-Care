// hotelData.js - Hotel names and their associated unit numbers

const hotelUnitMap = {
    "Bronze Beach": [1, 3, 5, 6, 7, 8, 9, 10, 11, 12, 14, 16, 17, 18, 19, 25, 26],
    "Bronze Bay": [1, 2, 3, 6, 8, 10, 11, 12, 15, 17, 19, 21, 24, 25, 26],
    "Bensiesta": [201, 302],
    "Sea Lodge": [12, 14, 45, 53, 64, 72, 84, 92],
    "Sea Breeze": [4],
    "Terra Mare": [108],
    "Kyalanga": [17, 27],
    "Lighthouse": [201],
    "Glitter Bay": [15]
};

// Returns the full hotel options HTML string (used when building rows dynamically)
function getHotelOptionsHTML(selectedHotel = "") {
    const hotels = [
        "Beacon-Rock", "Bensiesta", "Berumdas", "Bronze Bay", "Bronze Beach",
        "Breakers", "Cormoran", "Glitter Bay", "Kyalanga", "Lighthouse",
        "Malindi", "Marine", "Oyster Rock", "Pearls", "Sea Lodge",
        "Sea Breeze", "Shades", "Terra Mare"
    ];
    return hotels.map(h =>
        `<option value="${h}" ${h === selectedHotel ? "selected" : ""}>${h === "Berumdas" ? "Bermudas" : h}</option>`
    ).join('');
}

function updateUnitDatalist(hotelSelect) {
    const unitInput = hotelSelect.closest('tr').querySelector('.unit-number');
    const datalistId = 'unit-options-' + Math.random().toString(36).substr(2, 5);
    let datalist = document.createElement('datalist');
    datalist.id = datalistId;

    const selectedHotel = hotelSelect.value;
    const units = hotelUnitMap[selectedHotel] || [];

    datalist.innerHTML = units.map(unit => `<option value="${unit}">`).join("");
    document.body.appendChild(datalist);

    unitInput.setAttribute('list', datalistId);
}

// On page load, restore previously selected hotel from localStorage
window.addEventListener('load', function () {
    const selectedHotel = localStorage.getItem('selectedHotel');
    const hotelSelect = document.getElementById('hotelSelect');
    if (selectedHotel && hotelSelect) {
        hotelSelect.value = selectedHotel;
    }
});

// Save selected hotel to localStorage on change
document.addEventListener('DOMContentLoaded', function () {
    const hotelSelect = document.getElementById('hotelSelect');
    if (hotelSelect) {
        hotelSelect.addEventListener('change', function () {
            localStorage.setItem('selectedHotel', this.value);
        });
    }
});

function getDepHotels() { return Object.keys(DEP_BEDS).sort((a, b) => a.localeCompare(b)); }
function getDepUnits(hotel) { return Object.keys(DEP_BEDS[hotel] || {}).map(Number).sort((a, b) => a - b); }
function getDepBeds(hotel, unit) { return (DEP_BEDS[hotel] || {})[Number(unit)] ?? null; }
function getDepCodeForUnit(hotel, unit) { const b = getDepBeds(hotel, unit); return b ? "DEP" + b : ""; }
function isValidDepUnit(hotel, unit) { return Object.prototype.hasOwnProperty.call(DEP_BEDS[hotel] || {}, Number(unit)); }
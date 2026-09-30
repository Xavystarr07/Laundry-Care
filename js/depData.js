// depData.js - Departure prices + hotel → unit → bedroom map
// Bedroom counts come from the handwritten notes.
// null = unit exists but bedroom count unknown (you pick DEP1-4 manually).

const DEP_PRICES = {
    DEP1: { label: "1 full bedroom + towels",  beds: 1, price: 129.86 },
    DEP2: { label: "2 full bedrooms + towels", beds: 2, price: 225.11 },
    DEP3: { label: "3 full bedrooms + towels", beds: 3, price: 292.66 },
    DEP4: { label: "4 full bedrooms + towels", beds: 4, price: 323.85 }
};

// hotel → { unit: bedrooms }
const DEP_BEDS = {
    "Bronze Bay": {
        1: 2, 2: 2, 3: 2, 6: 2, 8: 2, 10: 2, 11: 2, 12: 2, 15: 2, 17: 2, 19: 2,
        21: 1, 24: 1,
        25: 3, 26: 3
    },
    "Bronze Beach": {
        1: 2, 3: 2, 5: 2, 6: 2, 7: 2, 8: 2, 9: 2, 10: 2, 11: 2, 12: 2,
        14: 2, 16: null, 17: 2, 18: 2, 19: 2,
        25: 3, 26: 3
    },
    "Breakers": {
        128: 2, 131: 2, 226: 2, 228: 2, 231: 2, 311: 2, 331: 2, 423: 2, 515: 2, 516: 2,
        210: 3,
        422: 1, 512: 1
    },
    "Sea Lodge":    { 12: 3, 14: 3, 45: null, 53: 3, 64: 3, 72: 3, 84: 3, 92: null },
    "Terra Mare":   { 108: 3 },
    "Cormoran":     { 10: 3, 25: 3, 31: 4 },
    "Glitter Bay":  { 15: 3 },
    "Kyalanga":     { 17: 3, 27: 3 },
    "Lighthouse":   { 201: 2, 204: 1 },
    "Marine":       { 35: 4 },
    "Oceans":       { 2106: 2 },
    "Pearls":       { 14: 1, 43: 2 },
    "Sea Breeze":   { 4: 2 },
    "Bensiesta":    { 201: null, 302: null },

    // No bedroom info yet - add units here when you have them
    "Beacon-Rock": {},
    "Bermudas":    {},
    "Malindi":     {},
    "Oyster Rock": {},
    "Shades":      {}
};

// All hotels, alphabetical (matches your existing dropdown names)
function getDepHotels() {
    return Object.keys(DEP_BEDS).sort((a, b) => a.localeCompare(b));
}

// Units for a hotel, sorted numerically
function getDepUnits(hotel) {
    return Object.keys(DEP_BEDS[hotel] || {}).map(Number).sort((a, b) => a - b);
}

// Bedroom count for a unit, or null if unknown
function getDepBeds(hotel, unit) {
    const beds = (DEP_BEDS[hotel] || {})[Number(unit)];
    return beds ?? null;
}

// Auto-picked DEP code for a unit, or '' if unknown
function getDepCodeForUnit(hotel, unit) {
    const beds = getDepBeds(hotel, unit);
    return beds ? "DEP" + beds : "";
}

function isValidDepUnit(hotel, unit) {
    return Object.prototype.hasOwnProperty.call(DEP_BEDS[hotel] || {}, Number(unit));
}
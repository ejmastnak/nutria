// Rounds inputted number to the given precision
export const round = (number, precision=0) => {
    if (number === null) return null;
    if (isNaN(number)) return NaN;
    return Math.round((number + Number.EPSILON) * (10**precision)) / (10**precision)
}

// Rounds inputted number to the given number of nonzero decimals
// See https://stackoverflow.com/questions/38837610/javascript-how-to-round-number-to-2-non-zero-decimals
export const roundNonZero = (num, decimals=0) => {
  num = Number(num);
  if (isNaN(num)) return NaN;
  const log10 = num ? Math.floor(Math.log10(num)) : 0
  const div = log10 < 0 ? Math.pow(10, decimals - log10 - 1) : Math.pow(10, decimals);
  return Math.round(num * div) / div;
}

// This is a hack: at the time of writing this, I did not know how to locally
// use an exported function without consequences in the exporting process.
function localRound (number, precision=0) {
  return Math.round((number + Number.EPSILON) * (10**precision)) / (10**precision)
}

// Prepares units for specifying amount of an ingredient or meal.
// Input: all mass and volume units, plus any ingredient/meal-specific units.
// Filters out volume units if passed densityGMl is null, and adds a
// displayName key/value pair with gram equivalent amount of unit (for
// human-facing display).
export const prepareUnitsForDisplay = function(units, densityGMl) {
  return units.filter(unit =>
    unit.g || (densityGMl && unit.ml) || unit.custom_grams
  ).map((unit) => {
      if (unit.name === 'g') {
        unit['display_name'] = unit.name
        return unit
      } else if (unit.g) {
        unit['display_name'] = unit.name + " (" + localRound(Number(unit.g)) + " g)"
        return unit
      } else if (densityGMl && unit.ml) {
        unit['display_name'] = unit.name + " (" + localRound(Number(unit.ml * densityGMl)) + " g)"
        return unit
      } else if (unit.custom_grams) {
        unit['display_name'] = unit.name + " (" + localRound(Number(unit.custom_grams)) + " g)"
        return unit
      } else {
        unit['display_name'] = unit.name
        return unit
      }
    })
}

// Converts inputted amount of inputted unit to gram
export const gramAmountOfUnit = function(amount, unit, densityGMl) {
  if (unit.g) {
    return amount * Number(unit.g)
  } else if (densityGMl && unit.ml) {
    return amount * Number(unit.ml * densityGMl)
  } else if (unit.custom_grams) {
    return amount * Number(unit.custom_grams)
  } else {
    return amount
  }
}

// Returns current date in YYYY-MM-DD format
export const nowYYYYMMDD = function() {
  const date = new Date()
  var month = '' + (date.getMonth() + 1)
  var day = '' + date.getDate()
  var year = date.getFullYear()

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return [year, month, day].join('-');
}


// Returns current time in HH-mm format
export const nowHHmm = function() {
  const date = new Date()
  var h = date.getHours()
  var m = date.getMinutes()
  if (h.length < 2) h = '0' + h;
  if (m.length < 2) m = '0' + m;
  return h + ':' + m
}

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

// Returns current local date in YYYY-MM-DD format
export const getCurrentLocalYYYYMMDD = function() {
  const date = new Date()
  var month = String((date.getMonth() + 1))
  var day = String(date.getDate())
  var year = date.getFullYear()

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return [year, month, day].join('-');
}


// Returns current local time in HH-mm format
export const getCurrentLocalHHmm = function() {
  const date = new Date()
  var h = String(date.getHours())
  var m = String(date.getMinutes())
  if (h.length < 2) h = '0' + h;
  if (m.length < 2) m = '0' + m;
  return h + ':' + m
}

// Input a "YYYY-MM-DD HH:mm:ss" string representing a UTC date time.
// Output a "YYYY-MM-DD" string representing date in local time.
// Yes it is a bit ridiculous doing this manually, but I prefer not to rely on
// Date.toLocaleDateString().
export const getLocalYYYYMMDD = function (dateTimeUTC) {
    const y = dateTimeUTC.substring(0, 4)
    const mo = Number(dateTimeUTC.substring(5, 7)) - 1
    const d = dateTimeUTC.substring(8, 10)
    const h = dateTimeUTC.substring(11, 13)
    const min = dateTimeUTC.substring(14, 16)
    const s = dateTimeUTC.substring(17, 19)
    const localDate = new Date(Date.UTC(y, mo, d, h, min, s))

    const localYYYY = String(localDate.getFullYear())
    var localMM = String(localDate.getMonth() + 1)
    var localDD = String(localDate.getDate())
    if (localMM.length < 2) localMM = '0' + localMM;
    if (localDD.length < 2) localDD = '0' + localDD;

    return localYYYY + "-" + localMM + "-" + localDD
}

// Input a "YYYY-MM-DD HH:mm:ss" string representing a UTC date time.
// Output a "HH:mm" string representing time in local time.
// Yes it is a bit ridiculous doing this manually, but I prefer not to rely on
// Date.toLocaleTimeString().
export const getLocalHHMM = function (dateTimeUTC) {
    const y = dateTimeUTC.substring(0, 4)
    const mo = Number(dateTimeUTC.substring(5, 7)) - 1
    const d = dateTimeUTC.substring(8, 10)
    const h = dateTimeUTC.substring(11, 13)
    const min = dateTimeUTC.substring(14, 16)
    const s = dateTimeUTC.substring(17, 19)
    const localDate = new Date(Date.UTC(y, mo, d, h, min, s))

    var localHH = String(localDate.getHours())
    var localMM = String(localDate.getMinutes())
    if (localHH.length < 2) localHH = '0' + localHH;
    if (localMM.length < 2) localMM = '0' + localMM;

    return localHH + ":" + localMM
}

// Input a "YYYY-MM-DD HH:mm:ss" string representing a UTC date time.
// Output a human-readable representation of the date (the date only, not also
// the time) in a format similar to en-GB, e.g. "1 January 1970".
export const getHumanReadableLocalDate = function (dateTimeUTC, shortMonth=false) {
    const y = dateTimeUTC.substring(0, 4)
    const mo = Number(dateTimeUTC.substring(5, 7)) - 1
    const d = dateTimeUTC.substring(8, 10)
    const h = dateTimeUTC.substring(11, 13)
    const min = dateTimeUTC.substring(14, 16)
    const s = dateTimeUTC.substring(17, 19)
    const localDate = new Date(Date.UTC(y, mo, d, h, min, s))

    const months = shortMonth
        ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    return localDate.getDate() + " " + months[localDate.getMonth()] + " " + localDate.getFullYear()
}

export const getHumanReadableLocalTime = function (dateTimeUTC) {
    const y = dateTimeUTC.substring(0, 4)
    const mo = Number(dateTimeUTC.substring(5, 7)) - 1
    const d = dateTimeUTC.substring(8, 10)
    const h = dateTimeUTC.substring(11, 13)
    const min = dateTimeUTC.substring(14, 16)
    const s = dateTimeUTC.substring(17, 19)
    const localDate = new Date(Date.UTC(y, mo, d, h, min, s))

    return localDate.toLocaleTimeString([], { hour: "numeric", minute: "2-digit" })
}

// Input a "YYYY-MM-DD HH:mm:ss" string representing a local date time.
// Output a "YYYY-MM-DD HH:mm:ss" string representing the corresponding UTC
// date time.
export const getUTCDateTime = function (localDateTimeString) {
    const y = localDateTimeString.substring(0, 4)
    const mo = Number(localDateTimeString.substring(5, 7)) - 1
    const d = localDateTimeString.substring(8, 10)
    const h = localDateTimeString.substring(11, 13)
    const min = localDateTimeString.substring(14, 16)
    const s = localDateTimeString.substring(17, 19)
    const localDate = new Date(y, mo, d, h, min, s)

    const utcY = String(localDate.getUTCFullYear())
    var utcMo = String(localDate.getUTCMonth() + 1)
    var utcD = String(localDate.getUTCDate())
    var utcH = String(localDate.getUTCHours())
    var utcMin = String(localDate.getUTCMinutes())
    var utcS = String(localDate.getUTCSeconds())

    if (utcMo.length < 2) utcMo = '0' + utcMo;
    if (utcD.length < 2) utcD = '0' + utcD;
    if (utcH.length < 2) utcH = '0' + utcH;
    if (utcMin.length < 2) utcMin = '0' + utcMin;
    if (utcS.length < 2) utcS = '0' + utcS;

    return utcY + "-" + utcMo + "-" + utcD + " " + utcH + ":" + utcMin + ":" + utcS
}

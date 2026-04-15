// Shared micro-interactions for TricyKab mockups.
// Goal: keep pages feeling like a real app prototype (no backend).

(() => {
  const storageKey = "tricykab_mockups";

  function load() {
    try {
      return JSON.parse(localStorage.getItem(storageKey) || "{}");
    } catch {
      return {};
    }
  }

  function save(patch) {
    const state = { ...load(), ...patch };
    localStorage.setItem(storageKey, JSON.stringify(state));
    return state;
  }

  function setRideType(type) {
    save({ rideType: type });
    document.documentElement.dataset.rideType = type;
  }

  function getRideType() {
    return load().rideType || "shared";
  }

  function setAvailability(isOnline) {
    save({ driverOnline: !!isOnline });
    document.documentElement.dataset.driverOnline = isOnline ? "1" : "0";
  }

  function getAvailability() {
    return load().driverOnline ?? true;
  }

  function init() {
    // Apply persisted state.
    document.documentElement.dataset.rideType = getRideType();
    document.documentElement.dataset.driverOnline = getAvailability() ? "1" : "0";
  }

  window.TricyKabMockups = {
    load,
    save,
    init,
    setRideType,
    getRideType,
    setAvailability,
    getAvailability,
  };

  document.addEventListener("DOMContentLoaded", init);
})();


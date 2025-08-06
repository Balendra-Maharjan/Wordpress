/**
 * Creates a debounced version of a function that delays its execution
 * until after a specified wait time has elapsed since the last call.
 *
 * @param {Function} func - The function to debounce
 * @param {number} wait - The number of milliseconds to delay
 * @param {Object} options - Additional options
 * @param {boolean} [options.leading=false] - Execute on the leading edge of the timeout
 * @param {boolean} [options.trailing=true] - Execute on the trailing edge of the timeout
 * @returns {Function} - The debounced function
 */
export function debounce(func, wait, options = {}) {
  let timeoutId = null;
  let lastArgs = null;
  let lastThis = null;
  let lastCallTime = null;
  const { leading = false, trailing = true } = options;

  function invokeFunc() {
    const args = lastArgs;
    const thisArg = lastThis;

    lastArgs = lastThis = null;
    lastCallTime = null;
    func.apply(thisArg, args);
  }

  function startTimer() {
    timeoutId = setTimeout(() => {
      if (trailing && lastArgs) {
        invokeFunc();
      }
      timeoutId = null;
    }, wait);
  }

  function cancelTimer() {
    if (timeoutId !== null) {
      clearTimeout(timeoutId);
      timeoutId = null;
    }
  }

  function debounced(...args) {
    const time = Date.now();
    lastArgs = args;
    lastThis = this;
    lastCallTime = time;

    if (timeoutId === null) {
      if (leading) {
        invokeFunc();
      }
      startTimer();
    } else {
      cancelTimer();
      startTimer();
    }
  }

  // Add method to cancel pending execution
  debounced.cancel = function() {
    if (timeoutId !== null) {
      cancelTimer();
      lastArgs = lastThis = lastCallTime = null;
    }
  };

  // Add method to flush pending execution
  debounced.flush = function() {
    if (timeoutId !== null) {
      cancelTimer();
      if (lastArgs) {
        invokeFunc();
      }
    }
  };

  return debounced;
}

/**
 * Creates an async debounced version of a function that works with promises
 * and maintains proper error handling.
 *
 * @param {Function} func - The async function to debounce
 * @param {number} wait - The number of milliseconds to delay
 * @param {Object} options - Additional options
 * @param {boolean} [options.leading=false] - Execute on the leading edge of the timeout
 * @param {boolean} [options.trailing=true] - Execute on the trailing edge of the timeout
 * @returns {Function} - The debounced async function
 */
export function debounceAsync(func, wait, options = {}) {
  let timeoutId = null;
  let lastArgs = null;
  let lastThis = null;
  let lastCallTime = null;
  let pendingPromise = null;
  const { leading = false, trailing = true } = options;

  async function invokeFunc() {
    const args = lastArgs;
    const thisArg = lastThis;

    lastArgs = lastThis = null;
    lastCallTime = null;

    try {
      const result = await func.apply(thisArg, args);
      pendingPromise = null;
      return result;
    } catch (error) {
      pendingPromise = null;
      throw error;
    }
  }

  function startTimer() {
    timeoutId = setTimeout(() => {
      if (trailing && lastArgs) {
        pendingPromise = invokeFunc();
      }
      timeoutId = null;
    }, wait);
  }

  function cancelTimer() {
    if (timeoutId !== null) {
      clearTimeout(timeoutId);
      timeoutId = null;
    }
  }

  function debounced(...args) {
    const time = Date.now();
    lastArgs = args;
    lastThis = this;
    lastCallTime = time;

    if (timeoutId === null) {
      if (leading) {
        pendingPromise = invokeFunc();
      }
      startTimer();
    } else {
      cancelTimer();
      startTimer();
    }

    return pendingPromise;
  }

  // Add method to cancel pending execution
  debounced.cancel = function() {
    if (timeoutId !== null) {
      cancelTimer();
      lastArgs = lastThis = lastCallTime = null;
      pendingPromise = null;
    }
  };

  // Add method to flush pending execution
  debounced.flush = async function() {
    if (timeoutId !== null) {
      cancelTimer();
      if (lastArgs) {
        return invokeFunc();
      }
    }
    return pendingPromise;
  };

  return debounced;
}
const u = require('../../utils');

const parseInput = () => u.parseInput('input.txt')
  .map(s => parseInt(s, 10));

const head = ([x]) => x;
const tail = ([, ...xs]) => xs;
const empty = a => !Array.isArray(a) || !a.length;

const solveP1 = () => parseInput()
  .reduce((a, b) => a + b, 0);

const solveP2 = () => {
  const input = parseInput();

  const recursive = (acc, rem, freqs) => {
    if (empty(rem)) {
      return {
        found: false,
        acc,
        freqs,
      };
    }

    const freq = acc + head(rem);
    if (freqs[freq]) {
      return {
        found: true,
        freq,
      };
    }

    const newFreqs = freqs;
    newFreqs[freq] = true;
    return recursive(freq, tail(rem), newFreqs);
  };

  let res = {
    acc: 0,
    freqs: {},
  };

  let i = 0;
  while (!res.found) {
    res = recursive(res.acc, input, res.freqs);
    i += 1;
  }

  console.log('Iterations of input needed:', i);

  return res.freq;
};

u.main(solveP2);

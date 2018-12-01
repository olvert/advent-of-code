const fs = require('fs');

const parseInput = () => fs.readFileSync('input.txt', 'utf-8')
  .toString()
  .split(/\r?\n/g)
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

const main = () => {
  const timeLabel = 'Elapsed time';
  const answerLabel = 'Answer:';

  console.time(timeLabel);
  const answer = solveP2();
  console.log(answerLabel, answer);
  console.timeEnd(timeLabel);
};

main();

const u = require('../../utils');

const parseInput = () => u.parseInput()
  .map(s => parseInt(s, 10));

const solveP1 = () => {
  const offsets = parseInput();

  let i = 0;
  let steps = 0;

  while (i < offsets.length) {
    const j = i + offsets[i];
    offsets[i] += 1;
    steps += 1;
    i = j;
  }

  return steps;
};

const solveP2 = () => {
  const offsets = parseInput();

  let i = 0;
  let steps = 0;

  while (i < offsets.length) {
    const j = i + offsets[i];
    offsets[i] += offsets[i] < 3 ? 1 : -1;
    steps += 1;
    i = j;
  }

  return steps;
};

u.main(solveP2);

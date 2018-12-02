const _ = require('underscore');
const u = require('../../utils');

const parseInput = () => _.head(u.parseInput())
  .split('')
  .map(s => parseInt(s, 10));

const solveP1 = () => {
  const input = parseInput();
  const len = input.length;

  return input.reduce((acc, d, i, arr) => (
    arr[(i + 1) % len] === d ? acc + d : acc
  ), 0);
};

const solveP2 = () => {
  const input = parseInput();
  const len = input.length;
  const offset = len / 2;

  return input.reduce((acc, d, i, arr) => (
    arr[(i + offset) % len] === d ? acc + d : acc
  ), 0);
};

u.main(solveP2);

const u = require('../../utils');

const parseInput = () => u.parseInput()
  .map(s => s.split(' '));

const solveP1 = () => parseInput()
  .map(ss => (ss.length === new Set(ss).size ? 1 : 0))
  .reduce((a, b) => a + b);

const solveP2 = () => parseInput()
  .map(ss => ss.map(s => s.split('').sort().join('')))
  .map(ss => (ss.length === new Set(ss).size ? 1 : 0))
  .reduce((a, b) => a + b);

u.main(solveP2);

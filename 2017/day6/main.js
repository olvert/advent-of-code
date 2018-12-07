const _ = require('underscore');
const u = require('../../utils');

const parseInput = () => u.parseInput()[0]
  .split('\t')
  .map(s => parseInt(s, 10));

const findLoop = (bs, cs) => {
  const banks = bs;
  const configs = cs;
  let cycles = 0;
  let infinite = false;

  while (!infinite) {
    let i = banks.indexOf(_.max(banks));
    let mem = banks[i];
    banks[i] = 0;

    while (mem > 0) {
      i = (i + 1) % banks.length;
      banks[i] += 1;
      mem -= 1;
    }

    cycles += 1;
    const dist = banks.join('#');
    if (configs[dist]) infinite = true;
    configs[dist] = true;
  }

  return [cycles, banks];
};

const solveP1 = () => findLoop(parseInput(), {})[0];

const solveP2 = () => {
  const p1 = findLoop(parseInput(), {});
  return findLoop(p1[1], { [p1[1].join('#')]: true })[0];
};

u.main(solveP2);

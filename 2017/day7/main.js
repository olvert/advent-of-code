const _ = require('underscore');
const u = require('../../utils');

const parseInput = () => u.parseInput()
  .map(s => s.replace(/ /g, '').split(/\(|\)|->/g).filter(Boolean))
  .map(t => ({
    n: t[0],
    w: t[1],
    cs: t[2] ? t[2].split(',') : [],
  }));

const solveP1 = () => {
  const towers = parseInput();
  const parents = {};

  towers.forEach((t) => {
    if (!parents[t.n]) parents[t.n] = 0;

    t.cs.forEach((c) => {
      parents[c] = 1;
    });
  });

  return _.chain(parents)
    .pairs()
    .min(p => p[1])
    .value()[0];
};

u.main(solveP1);

const _ = require('underscore');
const u = require('../../utils');

const parseInput = () => u.parseInput()
  .map(s => s.split('\t'))
  .map(r => r.map(s => parseInt(s, 10)));

const solveP1 = () => parseInput()
  .reduce((acc, r) => acc + _.max(r) - _.min(r), 0);

const solveP2 = () => {
  const inner = (d, ds) => {
    if (u.empty(ds)) { return NaN; }
    if (d % _.first(ds) === 0) { return d / _.first(ds); }
    return inner(d, _.rest(ds));
  };

  const outer = (ds) => {
    if (u.empty(ds)) { return NaN; }
    const res = inner(_.first(ds), _.rest(ds));
    if (!Number.isNaN(res)) { return res; }
    return outer(_.rest(ds));
  };

  return parseInput()
    .map(r => outer(r.sort((a, b) => b - a)))
    .reduce((a, b) => a + b, 0);
};

u.main(solveP2);

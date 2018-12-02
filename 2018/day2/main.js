const _ = require('underscore');
const u = require('../../utils');

const parseInput = () => u.parseInput()
  .map(s => s.split(''));

const solveP1 = () => {
  const freqs = parseInput()
    .map(ss => ss.reduce((a, c) => {
      const b = a;
      b[c] = b[c] ? b[c] += 1 : 1;
      return b;
    }, {}))
    .map((cc) => {
      const vs = _.values(cc);
      return {
        two: vs.includes(2) ? 1 : 0,
        three: vs.includes(3) ? 1 : 0,
      };
    })
    .reduce((a, o) => ({
      two: a.two + o.two,
      three: a.three + o.three,
    }));

  return freqs.two * freqs.three;
};

const solveP2 = () => {
  // Check if two ids have at most 1 differentiating character
  const match = (diff, id1, id2) => {
    if (diff > 1) { return false; }
    if (u.empty(id1) && u.empty(id2)) { return true; }

    const d = (_.head(id1) !== _.head(id2)) ? 1 : 0;
    return match(diff + d, _.rest(id1), _.rest(id2));
  };

  // Check current id for match among remaining ids in array
  const inner = (id, ids) => {
    if (u.empty(ids)) { return false; }

    if (match(0, id, _.head(ids))) {
      return {
        id1: id,
        id2: _.head(ids),
      };
    }

    return inner(id, _.rest(ids));
  };

  // Search for solution for each id and remainder of array
  const outer = (ids) => {
    if (u.empty(ids)) { return 'no solution found'; }

    const res = inner(_.head(ids), _.rest(ids));

    return res || outer(_.rest(ids));
  };

  // Construct answer string from the two ids in solution
  const removeDiff = (ss, a, b) => {
    if (u.empty(a) && u.empty(b)) { return ss; }

    const s = _.first(a) === _.first(b) ? _.first(a) : '';
    return removeDiff(ss + s, _.rest(a), _.rest(b));
  };

  const res = outer(parseInput());
  return removeDiff('', res.id1, res.id2);
};

u.main(solveP2);

const _ = require('underscore');
const u = require('../../utils');

// Mapping for parsed claim array
const m = {
  id: 0,
  x: 1,
  y: 2,
  length: 3,
  height: 4,
};

// Get all inches / coordinates covered by claim 'c'
const getInches = (c) => {
  const inches = [];

  for (let x = c[m.x]; x < c[m.x] + c[m.length]; x += 1) {
    for (let y = c[m.y]; y < c[m.y] + c[m.height]; y += 1) {
      inches.push([x, y]);
    }
  }

  return inches;
};

// Get hash from inch / coordinate tuple 't'
const getHash = t => `${t[0]}#${t[1]}`;

// Validate that inches 'is' for a claim is not overlapped on fabric 'f'
const validate = (f, [i, ...is]) => {
  if (_.isUndefined(i)) return true;
  return f[getHash(i)] > 1 ? false : validate(f, is);
};

// Find unqiue claim in claims 'cs' that is not overlapped in fabric 'f'
const find = (f, [c, ...cs]) => {
  if (_.isUndefined(c)) return 'no solution found';
  return validate(f, getInches(c)) ? c[m.id] : find(f, cs);
};

const parseInput = () => u.parseInput()
  .map(ss => ss.replace(/ /g, '')
    .split(/@|,|:|x/g)
    .map((s, i) => (i === 0 ? s : parseInt(s, 10))));

const solveP1 = () => _.chain(parseInput())
  .map(getInches)
  .reduce((a, v) => a.concat(v))
  .map(getHash)
  .countBy(_.identity)
  .values()
  .reduce((a, i) => (i > 1 ? a + 1 : a), 0)
  .value();

const solveP2 = () => {
  const claims = parseInput();
  const fabric = _.chain(claims)
    .map(getInches)
    .reduce((a, v) => a.concat(v))
    .map(getHash)
    .countBy(_.identity)
    .value();

  return find(fabric, claims);
};

u.main(solveP2);

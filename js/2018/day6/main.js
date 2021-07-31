const _ = require('underscore');
const u = require('../../utils');

const mdLimit = 10000;

const getBoundaries = cs => ({
  xMin: _.min(cs, c => c[0])[0],
  xMax: _.max(cs, c => c[0])[0],
  yMin: _.min(cs, c => c[1])[1],
  yMax: _.max(cs, c => c[1])[1],
});

const mDistance = (c1, c2) => Math.abs(c2[0] - c1[0]) + Math.abs(c2[1] - c1[1]);

const getHash = c => `${c[0]}#${c[1]}`;

const fillPlaneP2 = (cs, b) => {
  const plane = {};

  for (let x = b.xMin; x <= b.xMax; x += 1) {
    for (let y = b.yMin; y <= b.yMax; y += 1) {
      const c1 = [x, y];
      const mdSum = cs.reduce((a, c2) => a + mDistance(c1, c2), 0);
      if (mdSum < mdLimit) plane[getHash(c1)] = true;
    }
  }

  return plane;
};

const fillPlaneP1 = (cs, b) => {
  const plane = {};

  for (let x = b.xMin; x <= b.xMax; x += 1) {
    for (let y = b.yMin; y <= b.yMax; y += 1) {
      const c1 = [x, y];
      const closest = _.sortBy(cs, c2 => mDistance(c1, c2));
      const unique = mDistance(c1, closest[0]) !== mDistance(c1, closest[1]);
      plane[getHash(c1)] = unique ? getHash(closest[0]) : null;
    }
  }

  return plane;
};

const getAreas = (p) => {
  const as = {};

  _.pairs(p).forEach((x) => {
    if (!x[1]) return;
    as[x[1]] = as[x[1]] ? as[x[1]] + 1 : 1;
  });

  return as;
};

const getInfinteCoords = (p, b) => {
  const inf = {};

  for (let x = b.xMin; x <= b.xMax; x += 1) {
    const c1 = getHash([x, b.yMin]);
    const c2 = getHash([x, b.yMax]);
    if (p[c1]) inf[p[c1]] = true;
    if (p[c2]) inf[p[c2]] = true;
  }

  for (let y = b.yMin; y <= b.yMax; y += 1) {
    const c1 = getHash([b.xMin, y]);
    const c2 = getHash([b.xMax, y]);
    if (p[c1]) inf[p[c1]] = true;
    if (p[c2]) inf[p[c2]] = true;
  }

  return Object.keys(inf);
};

const parseInput = () => u.parseInput('input.txt')
  .map(s => s.replace(/ /g, '')
    .split(',')
    .map(d => parseInt(d, 10)));

const solveP1 = () => {
  const cs = parseInput();
  const b = getBoundaries(cs);
  const p = fillPlaneP1(cs, b);
  const as = getAreas(p);
  const inf = getInfinteCoords(p, b);

  return _.chain(as)
    .pairs()
    .filter(x => !inf.includes(x[0]))
    .max(x => x[1])
    .value()[1];
};

const solveP2 = () => {
  const cs = parseInput();
  const b = getBoundaries(cs);
  const p = fillPlaneP2(cs, b);

  return Object.keys(p).length;
};

u.main(solveP2);

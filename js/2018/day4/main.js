const _ = require('underscore');
const u = require('../../utils');

const AWAKE = 'wakes up';
const ASLEEP = 'falls asleep';

const parseNote = n => (n === AWAKE || n === ASLEEP ? n : n.split(' ')[1]);

const isAwake = r => r[1] === AWAKE;
const isAsleep = r => r[1] === ASLEEP;

const updateSleep = (s, g, d1, d2) => {
  const s2 = s;
  const t1 = d1.getMinutes();
  const t2 = d2.getMinutes();

  if (!s2[g]) s2[g] = Array(60).fill(0);

  for (let i = t1; i < t2; i += 1) {
    s2[g][i] += 1;
  }

  return s2;
};

const travel = (s, g, t, [r, ...rs]) => {
  if (_.isUndefined(r)) return s;
  if (isAsleep(r)) return travel(s, g, r[0], rs);
  if (isAwake(r)) return travel(updateSleep(s, g, t, r[0]), g, null, rs);
  return travel(s, r[1], null, rs);
};

const parseInput = () => u.parseInput()
  .map(s => s.split(/\[|\] /g).slice(1))
  .map(([t, n]) => [new Date(t), parseNote(n)])
  .sort((a, b) => a[0] - b[0]);

const solveP1 = () => {
  const sleep = travel({}, null, null, parseInput());
  const guard = _.chain(sleep)
    .pairs()
    .map(g => [g[0], g[1], g[1].reduce((a, b) => a + b, 0)])
    .max(g => g[2])
    .value();

  const id = parseInt(guard[0].replace('#', ''), 10);
  const max = _.max(guard[1]);
  const index = guard[1].indexOf(max);
  return id * index;
};

const solveP2 = () => {
  const sleep = travel({}, null, null, parseInput());
  const guard = _.chain(sleep)
    .pairs()
    .map(g => [g[0], g[1], _.max(g[1])])
    .max(g => g[2])
    .value();

  const id = parseInt(guard[0].replace('#', ''), 10);
  const max = _.max(guard[1]);
  const index = guard[1].indexOf(max);
  return id * index;
};

u.main(solveP2);

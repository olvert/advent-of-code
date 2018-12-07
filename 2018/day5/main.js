const u = require('../../utils');

const lowercase = [...Array(26).keys()].map(i => String.fromCharCode(i + 97));
const uppercase = [...Array(26).keys()].map(i => String.fromCharCode(i + 65));

const getMapping = () => {
  const m = {};

  for (let i = 0; i < lowercase.length; i += 1) {
    m[lowercase[i]] = uppercase[i];
  }

  for (let i = 0; i < uppercase.length; i += 1) {
    m[uppercase[i]] = lowercase[i];
  }

  return m;
};

const getVariants = (pps, m) => lowercase.map(c => pps.filter(d => d !== c && d !== m[c]));

const react = (pps, m) => {
  const ps = pps;
  let reaction = true;

  while (reaction) {
    reaction = false;
    for (let i = 0; i < ps.length - 1; i += 1) {
      if (m[ps[i]] === ps[i + 1]) {
        ps.splice(i, 2);
        reaction = true;
        break;
      }
    }
  }

  return ps;
};

const parseInput = () => u.parseInput()[0].split('');

const solveP1 = () => react(parseInput(), getMapping()).length;

const solveP2 = () => {
  const m = getMapping();
  const lengths = getVariants(parseInput(), m)
    .map(v => react(v, m).length);

  return Math.min(...lengths);
};

u.main(solveP2);

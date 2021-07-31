const _ = require('underscore');
const u = require('../../utils');

const noise = ['Step ', ' must be finished before step ', ' can begin.'];

const getAllNodes = es => Array.from(new Set(_.flatten(es)));

const getNodesWithIncomingEdges = es => Array.from(new Set(_.pluck(es, 1)));

const getNodesWithoutIncomingEdges = (ns, es) => _.difference(ns, getNodesWithIncomingEdges(es)).sort();

const removeEdges = (es, n) => es.filter(e => e[0] !== n);

const parseInput = () => u.parseInput('test_input.txt')
  .map(s => s
    .replace(noise[0], '')
    .replace(noise[1], ',')
    .replace(noise[2], '')
    .split(','));

const solveP1 = () => {
  const solution = [];
  let edges = parseInput();
  let remaining = getAllNodes(edges);
  let availabe = getNodesWithoutIncomingEdges(remaining, edges);

  while (availabe.length) {
    const n = _.first(availabe);
    // availabe = _.rest(availabe);
    solution.push(n);
    edges = removeEdges(edges);
  }

  // getAllNodes(ms);
  return availabe;
};

u.main(solveP1);

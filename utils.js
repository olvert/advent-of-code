const fs = require('fs');

const main = (solve) => {
  const timeLabel = 'Elapsed time';
  const answerLabel = 'Answer:';

  console.time(timeLabel);
  const answer = solve();
  console.log(answerLabel, answer);
  console.timeEnd(timeLabel);
};

const parseInput = (file = 'input.txt') => fs.readFileSync(file, 'utf-8')
  .toString()
  .split(/\r?\n/g);

module.exports = {
  main,
  parseInput,
};

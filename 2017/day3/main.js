const u = require('../../utils');

const input = 312051;

const walk = (c) => {
  // Based on: https://stackoverflow.com/a/33639875/3000362

  let x = 0;
  let y = 0;
  let d = 1;
  let m = 1;
  let i = 1;

  while (i < c) {
    while (2 * x * d < m && i < c) {
      x += d;
      i += 1;
    }
    while (2 * y * d < m && i < c) {
      y += d;
      i += 1;
    }

    d *= -1;
    m += 1;
  }

  return { x, y };
};

const walkAdjacent = (c) => {
  let x = 0;
  let y = 0;
  let d = 1;
  let m = 1;
  let i = 1;

  const plane = { 0: { 0: 1 } };

  // Get value at coord
  const get = (cx, cy) => {
    if (!(cx in plane)) { plane[cx] = {}; }
    return plane[cx][cy] || 0;
  };

  // Set value at coord
  const set = (cx, cy, v) => {
    if (!(cx in plane)) { plane[cx] = {}; }
    plane[cx][cy] = v;
  };

  // Get adjacent sum for coord
  const sum = (cx, cy) => {
    const adj = [
      { x: cx + 1, y: cy },
      { x: cx + 1, y: cy + 1 },
      { x: cx, y: cy + 1 },
      { x: cx - 1, y: cy + 1 },
      { x: cx - 1, y: cy },
      { x: cx - 1, y: cy - 1 },
      { x: cx, y: cy - 1 },
      { x: cx + 1, y: cy - 1 },
    ];

    return adj.reduce((a, o) => a + get(o.x, o.y), 0);
  };

  while (i <= c) {
    while (2 * x * d < m && i <= c) {
      x += d;
      i = sum(x, y);
      set(x, y, i);
    }
    while (2 * y * d < m && i <= c) {
      y += d;
      i = sum(x, y);
      set(x, y, i);
    }

    d *= -1;
    m += 1;
  }

  // console.log({ plane });
  return i;
};

const solveP1 = () => {
  const coord = walk(input);
  return Math.abs(coord.x) + Math.abs(coord.y);
};

const solveP2 = () => walkAdjacent(input);

u.main(solveP2);

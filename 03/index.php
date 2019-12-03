<!doctype html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Slide puzzle</title>
	<style>
		* { font-family: Arial; box-sizing: border-box;}
		.page-wrapper { max-width: 1280px; margin: auto; }
		header, footer, .content { overflow: auto; }
		.main, .extras { float: left; padding: 8px;}
		.main { width: 66.666%; }
		.extras { width: 33.333%; clear:  right;}
		.hidden { display: none; }
		.svg-map { width: 100%; height: 100vh; }
    </style>
    <script src = "//code.jquery.com/jquery-3.3.1.min.js"></script>
	<script>
        var columns = 4,
            rows = 4;
        
        var canvas;

        var tileWidth,
            tileHeight,
            imgData,
            imgW,
            imgH,
            ctx,
            img,
            tiles;

        function drawImage(){
            ctx = $("#puzzle-canvas")[0].getContext("2d"),
            img = new Image();
    
            img.onload = function(){
                init();
                tiles = generateTiles();
                // draw tiles separated by offset on canvas
                drawTiles(tiles);
            };
            img.src = "./img/" + $("#select-image")[0].value;
        }

        /**
        Initialize image in canvas
         */
        function init() {
            imgW = img.width;
	        imgH = img.height;
            // Draw image in canvas 
            // (substract from total size of canvas width/heigh of offset space between tiles)
            ctx.drawImage(img, 0, 0, imgW,    imgH,     // source rectangle
                   0, 0, canvas.width-42, canvas.height-42); // destination rectangle
            // we need whole image data so we can split it into tiles
            imgData = ctx.getImageData(0, 0, imgW, imgH).data;
            ctx.clearRect(0, 0, 550, 550);
        }

        /**
        Split image data in tiles
        and add them to one array of all tiles
         */
        function generateTiles() {
	       
            var tiles = [];
            // keep index of every tile (so we can figure by which order should they go in final solution)
            var index = 1;
	        for (var yi = 0; yi < rows; yi++) {
		        for (var xi = 0; xi < columns; xi++) {
                    var tile = getTile(xi * tileWidth, yi * tileHeight);
                    tile.index = index++;
			        tiles.push(tile);
	            }
            }

            // get bottom row right most tile (last one)
            var imdata = tiles[tiles.length - 1].data;

            var r,g,b,avg;
            // replace last tile with gray square
            // - it will mark empty space
            for(var p = 0, len = imdata.length; p < len; p+=4) {
                imdata[p] = imdata[p+1] = imdata[p+2] = 166;
            }

            // last tile will be empty space where we can move neighbour tiles
            tiles[tiles.length - 1].index = 0;
	        
            return tiles;
        }

        /**
        Get tile in x column, y row, with part of image in that place
         */
        function getTile(x, y) {
	        var tile = [];
	        for (var i = 0; i < tileWidth; i++) {
                //slice original image from x to x + tilwWidth, concat
		        tile.push(...imgData.slice(getIndex(x, y + i), getIndex(x + tileWidth, y + i)));
	        }
	        //convert back to typed array and to imgdata object
	        tile = new ImageData(new Uint8ClampedArray(tile), tileWidth, tileHeight);
	        //save original position
	        tile.x = x;
	        tile.y = y;

	        return tile;
        }

        function getIndex(x, y) {
	        var i = indexX(x) + indexY(y);
	        if (i > imgData.length) console.warn("XY out of bounds");
	        return i;
        }

        //get imgdata index from img px positions
        function indexX(x) {
	        var i = x * 4;
	        if (i > imgData.length) console.warn("X out of bounds");
	        return i;
        }

        function indexY(y) {
            var i = imgW * 4 * y;
            if (i > imgData.length) console.warn("Y out of bounds");
            return i;
        }

        // distance between tiles
        var offset = 1.1;

        /**
        Draw tiles with image data {d} on canvas
         */
        function drawTiles(tiles) {
	        tiles.forEach((d,i) => ctx.putImageData(d, d.x * offset, d.y * offset));
        }

        window.onload = function() {
            canvas = $("#puzzle-canvas")[0];

            // calculate width and height of tiles considering # of rows and columns
            // and substract distance space between every tile
            tileWidth  = Math.floor(canvas.width / columns) - 14;
            tileHeight = Math.floor(canvas.height / rows) - 14;

            drawImage();
        };

        /**
        Mix tiles positions
         */
        function shuffleTiles(tiles) {
            var j, x, i, y;
            for (i = tiles.length - 1; i > 0; i--) {
                j = Math.floor(Math.random() * (i + 1));
                // save coordinates of tile, and original tile object
                // we need to save original tile because we need to mix tile's indexes also
                x = tiles[i].x;
                y = tiles[i].y;
                var tempTile = tiles[i];

                tiles[i].x = tiles[j].x;
                tiles[i].y = tiles[j].y;

                tiles[i] = tiles[j];

                tiles[j].x = x;
                tiles[j].y = y;

                tiles[j] = tempTile;
            }

            return tiles;
        }

        /**
        Shuffle tiles
         */
        function shufflePuzzle() {
            // shuffle tiles, and check if it's solvable
            // in case not, shuffle it until configuration is solvable
            var puzzle = shuffleTiles(tiles);

            while(!isSolvable(puzzle)) {
                puzzle = shuffleTiles(tiles);
            }

            // redraw mixed tiles on canvas
            ctx.clearRect(0, 0, 550, 550);
            drawTiles(puzzle);
        }

        function isSolvable(puzzle) {
            var parity = 0;
            var gridWidth = columns;
            var row = 0; // the current row we are on
            var blankRow = 0; // the row with the blank tile

            for (var i = 0; i < puzzle.length; i++) {
                if (i % gridWidth == 0) { // advance to next row
                    row++;
                }

                if (puzzle[i].index == 0) { // the blank tile
                    blankRow = row; // save the row on which encountered
                    continue;
                }

                // compare tiles indexes
                for (var j = i + 1; j < puzzle.length; j++) {
                    if (puzzle[i].index > puzzle[j].index && puzzle[j].index != 0) {
                        parity++;
                    }
                }
            }

            if (blankRow % 2 == 0) { // blank on odd row; counting from bottom
                return parity % 2 == 0;
            } else { // blank on even row; counting from bottom
                return parity % 2 != 0;
            }
        }

        function onCanvasClick(e) {
            var mousePos = getMousePos(canvas, e);
            console.log(mousePos.x + ',' + mousePos.y);
        }

        function getMousePos(canvas, e) {
            var rect = canvas.getBoundingClientRect();
            return {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        }
	</script>

	<?php include 'puzzle.php';?>
</head>
<body>
	<div class="page-wrapper">
		<div class="content">
            <div class="header">
                <select id="select-image" onchange="drawImage()">
                <?php 
                    $images = getPuzzleImages();

                    foreach($images as $item) {
                        echo '<option value="' . $item['image'] . '">' . $item['title'] . '</option>';
                    }
                ?>
                </select>
                <button type="button" onclick="shufflePuzzle()">Shuffle</button>
            </div>
			<div class="main">
                <canvas id="puzzle-canvas" width="550" height="550" onclick="onCanvasClick(event)"></canvas>
			</div>
		</div>
	</div>
</body>
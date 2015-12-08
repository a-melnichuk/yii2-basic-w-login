var Function = function(Plotty,errDiv,errText){
	this.expr = 0;
	this.expressionSet = false;
	this.obj = Plotty;
	this.errDiv = errDiv;
	this.errText = errText;
	
	var functionOptions = Plotty.getFunctionOptions();
	this.maxRecursions =  functionOptions.maxRecursions;
	this.precision = functionOptions.precision;
	this.color = functionOptions.color;
	this.lineWidth = functionOptions.lineWidth;
	this.functionPointColor = functionOptions.functionPointColor;
	
	var functionDataTable = Plotty.getFunctionDataTable();
	this.font = functionDataTable.font;
	this.fontSize = functionDataTable.fontSize;
	this.fontColor = functionDataTable.fontColor;
	this.fontUnits = functionDataTable.fontUnits;
	this.placesRounded = functionDataTable.placesRounded;
	this.lineSpacing = functionDataTable.lineSpacing;
	
	this.evalAtX = function(x){
		var varStr= this.expr.variables()[0];
		var obj = {};
		obj[varStr] = x;
		return this.expr.evaluate(obj);
	}
	
	this.checkVariables = function(expr,vars){
		var varsObj = {};
		for(var i=0;i < vars.length;++i){
			if(vars[i]!= 'x'){
				varsObj[vars[i]] = 0;
			}
		}
		return expr.simplify(varsObj);
	}
	
	this.handleError = function(e){
		if(e == "TypeError: expr.variables is not a function") return;
		// set error message and show error div
		this.expressionSet = false;
		this.errText.innerHTML = e;
		this.errDiv.style.display = 'block';
	}
	
	this.undefOrInf= function(num){
		if(isNaN(num)|| !isFinite(num) ) return true;
		return false;
	}
}

Function.prototype.setExpression = function(expr){
		var variables = expr.variables();
		this.expr = (variables.length > 1) ? this.checkVariables(expr,variables) : expr;
		this.expressionSet = true;
}

Function.prototype.evalExpression = function(x){
	try{
		var expr = this.expr;
		var variables = expr.variables();
		return (variables.length > 0)? this.evalAtX(x) : expr.evaluate();
	} catch(e){
		if(e instanceof TypeError){return;}
		logMyErrors(e); 
	}
}
Function.prototype.toFunc = function(scale,unitsX,y0){
		return -scale*this.evalExpression(unitsX)+y0;
}

Function.prototype.recursiveSubdiv = function(iters,maxIters,scale,y0,unitsX,unitsDx,x,dx){
	// at interval (a,c), create mid point b
	// create two additional mid points: a1 at (a+b)/2 and b1 at (b+c)/2
	// evaluate functions at given points

	var a = this.toFunc(scale,unitsX,y0);
	var c = this.toFunc(scale,unitsDx,y0);
	var bX = (unitsX+unitsDx)/2;
	var a1X = (unitsX+bX)/2;
	var b1X = (bX+unitsDx)/2;
	var b = this.toFunc(scale,bX,y0);
	var a1 = this.toFunc(scale,a1X,y0);
	var b1 = this.toFunc(scale,b1X,y0);
	var slope = (a-c)/(x-dx);

	//if all points are infities, or undefined, stop recursions
	if(this.undefOrInf(a)&&this.undefOrInf(a1)&&this.undefOrInf(b)&&this.undefOrInf(b1)&&this.undefOrInf(c)){
		return;
	}
	
	var cnt = 0;
	//check if there are infinities or discontinoities
	if(this.undefOrInf(a) ||this.undefOrInf(a1) ||this.undefOrInf(b) ||this.undefOrInf(b1) || this.undefOrInf(c) ){
		cnt++;
	}
	// determine if mid-points creating a zig-zag motion i.e. bigger or smaller, than their neighbours
	if( (a1>a && a1>b) || (a1<a && a1<b)){
		cnt++;
	}
	if( (b1>b && b1>c) || (b1<c && b1<b) ){
		cnt++;
	}
	

	//if one of the points is zig-zagging, infitiny,or undefined, subdivide graph
	var bXpx = (x+dx)/2;
	if( cnt>=1 && iters < maxIters){
		this.recursiveSubdiv(iters+1,maxIters,scale,y0,unitsX,bX,x,bXpx);
		this.recursiveSubdiv(iters+1,maxIters,scale,y0,bX,unitsDx,bXpx,dx);
		
	//otherwise, draw lines,connecting given points
	} else {
		var a1Xpx = (x+bXpx)/2;
		var b1Xpx = (bXpx+dx)/2;
		this.obj.drawLine(x,a,
                                  a1Xpx ,a1,
                                  this.color,this.lineWidth);
		this.obj.drawLine(a1Xpx, a1,
                                  bXpx,b,
                                  this.color,this.lineWidth);
		this.obj.drawLine(bXpx, b,
                                  b1Xpx,b1,
                                  this.color,this.lineWidth);
		this.obj.drawLine(b1Xpx, b1,
                                  dx,c,
                                  this.color,this.lineWidth);
	}					
}

Function.prototype.draw = function(){
	if(!this.expressionSet) return;
	var Plotty = this.obj;
	var metrics = Plotty.getMetrics();
	var len = metrics.canvasW;
	var divLen = metrics.scale * metrics.units * Plotty.step;
	var unitScale = metrics.scale * metrics.units;
	var dx =divLen/this.precision;
	var unitsAtBorderX = -Plotty.x0/unitScale;

	for(var x = len; x>=0;x-=dx){
		var unitsX = unitsAtBorderX + x/unitScale;
		var unitsDx = unitsX - dx/unitScale;
		this.recursiveSubdiv(0,this.maxRecursions,unitScale,Plotty.y0,unitsDx,unitsX,x-dx,x);
	}
}

Function.prototype.drawPointAtFunction = function(e){
	if(!this.expressionSet) return;
	var Plotty = this.obj;
	var metrics = Plotty.getMetrics();
	var x = Plotty.getX(e);
	var y0 = Plotty.y0;
	var unitScale = metrics.scale*metrics.units;
	var unitsX = (-Plotty.x0 + x)/unitScale;
	var y = -unitScale*this.evalExpression(unitsX) + y0;
	if(y < 0 || y > metrics.canvasH) return;
	var radius = 5;
	var ctx = Plotty.ctx;
	ctx.beginPath();
	ctx.arc(x, y, radius, 0, 2 * Math.PI, false);
	ctx.fillStyle = this.functionPointColor;
	ctx.fill();
}

Function.prototype.drawValuesTable = function(e){
	var Plotty = this.obj;
	var ctx = Plotty.ctx;
	var metrics = Plotty.getMetrics();
	var x = Plotty.getX(e);
	var y0 = Plotty.y0;
	var scale = metrics.scale;
	var unitScale = metrics.scale*metrics.units;
	var unitsX = Plotty.round( (-Plotty.x0 + x)/unitScale, this.placesRounded);
	var unitsY = Plotty.round (this.evalExpression(unitsX), this.placesRounded);
	var valX = "x: "+ unitsX;
	var valY = "y: "+  unitsY;

	ctx.font = this.fontSize+this.fontUnits +" "+ this.font;
	ctx.fillStyle = this.fontColor;
	if(!isNaN(unitsX))ctx.fillText(valX ,2*this.lineSpacing,this.lineSpacing+this.fontSize );
	if(!isNaN(unitsY))ctx.fillText(valY ,2*this.lineSpacing,4*this.lineSpacing+2*this.fontSize );
}


var Line = function(Plotty){

	this.obj = Plotty;
	this.color = Plotty.getLineOptions().color;
	this.lineWidth = Plotty.getLineOptions().lineWidth;
	
	var numberLineOptions = Plotty.getNumberLineOptions();
	this.divColor = numberLineOptions.separatorColor;
	this.font = numberLineOptions.font;
	this.fontColor = numberLineOptions.fontColor;
	this.fontSize = numberLineOptions.fontSize;
	this.fontUnits = numberLineOptions.fontUnits;
	this.placesRounded = numberLineOptions.placesRounded;
	this.exponentialPlacesRounded = numberLineOptions.exponentialPlacesRounded;

	
	this.getDirectionalOffset = function(pos,offset,len,directionalScalar){
		return (pos + offset >= len || pos + offset <= 0) ? offset*directionalScalar : offset;
	}	
	this.getDirectionalDivHeight = function(pos,divHeight,len,directionalScalar){
		return (pos >= len || (divHeight < 0 && pos <= 0)) ? divHeight*directionalScalar : divHeight;
	}
	this.handleMinMaxValBounds = function(val,minVal,maxVal){
		return (val <= minVal)? minVal : (val >= maxVal)? maxVal : val ;
	}
	this.getFontSettings = function(){
		return this.fontSize+this.fontUnits +" "+ this.font;
	}
	this.getExponentialObject = function(num,ctx){

		var str = Plotty.round(num,this.exponentialPlacesRounded).toExponential();
		var strWidth = ctx.measureText(str).width;
		var splitAt = str.indexOf("e");
		var scalar = str.substring(0,splitAt) + "\u2A2F10";
		var pow = str.substring(splitAt+1);
		var scalarWidth = ctx.measureText(scalar).width;
		return {pow:pow,scalar:scalar,scalarWidth:scalarWidth}
	}		
}

var AxisX = function(y,Plotty){
	Line.call(this,Plotty);
	this.y = y;
	var numberLineOptions = Plotty.getNumberLineOptions();
	this.divHeightX = numberLineOptions.divHeightX;
	this.fontOffsetY = numberLineOptions.fontOffsetY;
}
//inherit Line object
AxisX.prototype = Object.create(Line.prototype);

AxisX.prototype.drawNumberLine = function(){
	var Plotty = this.obj;
	var metrics = Plotty.getMetrics();
	var step = Plotty.step;
	var ctx = Plotty.ctx;
	var divLen = metrics.scale * metrics.units * step;
	// value at canvas border = (left border - origin x)/(divisor length). left border here is zero.
	var valBorderX = -Plotty.x0/divLen;//units
	// first divisor value 
	var divsBeforeX0 = Math.ceil(valBorderX);//units
	var firstStep = divLen*(divsBeforeX0 - valBorderX);//px
	var firstNum = divsBeforeX0*step;
	var offsetY = this.getDirectionalOffset(this.y,this.fontOffsetY,metrics.canvasH,-1);
	var divHeight = this.getDirectionalDivHeight(this.y,this.divHeightX,metrics.canvasH,-1);

	for(var x = firstStep;x < metrics.canvasW;x+=divLen){
		if(!Plotty.round(firstNum,this.exponentialPlacesRounded)==0){
			Plotty.drawLine(x,this.y,
								  x,this.y + divHeight,
								  this.divColor,this.lineWidth);

		ctx.font = this.getFontSettings();
		ctx.fillStyle = this.fontColor;
		if(step >= 1/Math.pow(10,this.placesRounded) && step <= Math.pow(10,this.placesRounded)){
			var num = Plotty.round(firstNum,this.placesRounded);
			var halfWidth = ctx.measureText(num).width/2;
			ctx.fillText( Plotty.round(firstNum,this.placesRounded) ,x - halfWidth,this.y+offsetY);
		}else 
			this.drawExponential(x,this.y+offsetY,firstNum,ctx);

		}
		firstNum+=step;
	}

}

// draw num in exponential form
AxisX.prototype.drawExponential = function(x,y,num,ctx){
	var expObj = this.getExponentialObject(num,ctx);
	ctx.fillText(expObj.scalar,x-expObj.scalarWidth/2,y);
	ctx.font = this.fontSize/1.25+""+this.fontUnits+" "+this.font;
	ctx.fillText( expObj.pow,x + expObj.scalarWidth/2 + this.fontSize/2,y - this.fontSize/2);
}

AxisX.prototype.draw = function(){
	var Plotty = this.obj;
	var metrics = Plotty.getMetrics();
	this.y = this.handleMinMaxValBounds(this.y,0,metrics.canvasH);
	Plotty.drawLine(0,this.y,
						  metrics.canvasW,this.y,
						  this.color,this.lineWidth);
	this.drawNumberLine();					  
}



var AxisY = function(x,Plotty){
	Line.call(this,Plotty);
	this.x = x;
	var numberLineOptions = Plotty.getNumberLineOptions();
	this.divHeightY = numberLineOptions.divHeightY;
	this.fontOffsetX = numberLineOptions.fontOffsetX;
}

//inherit Line object
AxisY.prototype = Object.create(Line.prototype);
		
AxisY.prototype.drawNumberLine = function(){
	var Plotty = this.obj;		
	var metrics = Plotty.getMetrics();
	var step = Plotty.step;
	var ctx = Plotty.ctx;
	var divLen = metrics.scale * metrics.units * Plotty.step;
	var canvasH = metrics.canvasH;
	var valBorderY = -Plotty.y0/divLen;//units
	var divsBeforeY0 = Math.ceil(valBorderY);//units
	var firstStep = divLen*(divsBeforeY0 - valBorderY);//px
	var firstNum = divsBeforeY0*step;
	var offsetX = this.getDirectionalOffset(this.x,this.fontOffsetX,metrics.canvasW,-4);
	var divHeight = this.getDirectionalDivHeight(this.x,this.divHeightY,metrics.canvasW,-1);
	
	for(var y = firstStep;y < canvasH;y+=divLen){
		if(!Plotty.round(firstNum,this.exponentialPlacesRounded)==0){
			Plotty.drawLine(this.x,y,
                                              this.x+divHeight,y,
                                              this.divColor,this.lineWidth);

			ctx.font = this.getFontSettings();
			ctx.fillStyle = this.fontColor;
			if(step >= 1/Math.pow(10,this.placesRounded) && step <= Math.pow(10,this.placesRounded)){
				ctx.fillText( -Plotty.round(firstNum,this.placesRounded) ,this.x+offsetX,y +this.fontSize/3);//reverse y sign
				
			} else 
				this.drawExponential(this.x+offsetX,y + this.fontSize/3,-firstNum,ctx);//reverse y sign
			
		}
		firstNum+=step;
	}
}

// draw num in exponential form
AxisY.prototype.drawExponential = function(x,y,num,ctx){
	var expObj = this.getExponentialObject(num,ctx);
	ctx.fillText(expObj.scalar,x,y);
	ctx.font = this.fontSize/1.25+""+this.fontUnits+" "+this.font;
	ctx.fillText( expObj.pow,x + expObj.scalarWidth,y - this.fontSize/2);
}	

AxisY.prototype.draw = function(){
	var Plotty = this.obj;
	var metrics = Plotty.getMetrics();
	this.x = this.handleMinMaxValBounds(this.x,0,metrics.canvasW);
	Plotty.drawLine(this.x,0,
						  this.x,metrics.canvasH,
						  this.color,this.lineWidth);
	this.drawNumberLine();
}	

	
var Plotty = function(canvas,errDiv,errText){
	var self = this;
	this.canvas = canvas;
	this.ctx = canvas.getContext("2d");
	this.bounds = canvas.getBoundingClientRect();
	this.dx = 0; 
	this.dy = 0;
	this.dragged = false; 
	
	this.options = {
		functionOptions : {
			color:"#00FF00",
			lineWidth:2,
			maxRecursions:30,//number of maximum recursive calls in curvy regions of graph
			precision: 50, // num thicks per length of one divisor
			functionPointColor:"#00FF00"
		},
		functionDataTable:{
			font:"Times",
			fontSize:16,
			fontColor:"#6666FF",
			fontUnits: "px",
			placesRounded: 6,
			lineSpacing:4
		},
		rectOptions: {
			color: "#EEF3ED"
		},
		lineOptions: {
			color: "#6666FF",
			lineWidth:2,
		},
		metrics:{
			scale:1,
			scaleFactor: 0.1,
			//Dont scale towards mouse direction if (mx-x0) or  (my-y0) lower, than this bound
			directionalScaleLowerBound: 20, //px , see scalegraph()
			units: 37.8*2.5,//1cm ~ 37.8px
			canvasX: self.bounds.left,
			canvasY: self.bounds.top,
			canvasW: self.canvas.width,
			canvasH: self.canvas.height,
			centerX: (self.bounds.right- self.bounds.left)/2,
			centerY: (self.bounds.bottom- self.bounds.top)/2
		},
		mouseLineOptions:{
			color: "#AAAAAA",
			lineWidth: 2,
		},
		numberLineOptions:{
			font:"Times",
			fontSize:16,
			fontColor:"#333333",
			fontUnits: "px",
			placesRounded: 4,
			exponentialPlacesRounded:8,
			fontOffsetX :15,
			fontOffsetY : 20,
			separatorColor: "#6666FF",
			divHeightX:5,
			divHeightY:5,
		}
	};
	
	
	
	//getters
	this.getMetrics = function(){
		return this.options.metrics;
	}
	
	this.getStep = function(){
		return this.step;
	}
	
	this.getFunctionOptions = function(){
		return this.options.functionOptions;
	} 
	
	this.getFunctionDataTable = function(){
		return this.options.functionDataTable;
	}
	
	this.getRectOptions= function(){
		return this.options.rectOptions;
	}
	this.getLineOptions= function(){
		return this.options.lineOptions;
	}
	this.getNumberLineOptions = function(){
		return this.options.numberLineOptions;
	}	
	
	this.getMouseLineOptions= function(){
		return this.options.mouseLineOptions;
	}
	this.getScale = function(){
		return self.getMetrics().scale;
	}
	this.getScaleFactor = function(){
		return self.getMetrics().scaleFactor;
	}
	
	this.getX = function(e){
		return e.clientX-self.getMetrics().canvasX;
	}
	this.getY = function(e){
		return e.clientY-self.getMetrics().canvasY;
	}
	
	//setters
	this.setScale = function(scale){
		self.getMetrics().scale = scale;
	}
	this.setStep = function(step){
		this.step = step;
	}
	this.setFunction = function(f){
		this.f = f;
	}

	//round a num, given places after zero
	this.round = function(num,places){
		var scalar = Math.pow(10,places);
		return Math.round(num * scalar)/scalar;
	}
	
	//draw horizontal and vertical lines through mouse position
	this.drawMouseLines = function(e){
		var ctx = self.ctx;
		var metrics = self.getMetrics();
		var lineWidth = self.getMouseLineOptions().lineWidth;
		
		ctx.fillStyle = self.getMouseLineOptions().color;
		ctx.lineWidth = lineWidth;
		ctx.fillRect(0,self.getY(e),metrics.canvasW,lineWidth);
		ctx.fillRect(self.getX(e),0,lineWidth ,metrics.canvasH);
	}
	
	this.drawLine = function(x,y,dx,dy,color,lineWidth){
		var canvasH = this.getMetrics().canvasH;
		if( (y<0 || y> canvasH)&&(dy<0 || dy > canvasH) ) return;
		var ctx = this.ctx;
		ctx.beginPath();
		ctx.moveTo( x, y);
		ctx.lineTo( dx,dy );
		ctx.strokeStyle = color;
		ctx.lineWidth = lineWidth;
		ctx.stroke();
		ctx.closePath();
	}
	
	
	this.refreshCanvas = function(e) {
		this.ctx.clearRect(0, 0, canvas.width, canvas.height);
		this.axisX.draw();
		this.axisY.draw();
		this.f.draw();
		this.f.drawPointAtFunction(e);
		this.f.drawValuesTable(e);
		//this.drawMouseLines(e);
	}
	
	//shifts num to ten's place, 
	//returns new num and decimal places shifted to potenially shift it back
	this.shiftToTens = function(num){
		var decPlaces = 1;
		while(num < 10){
			num*=10;
			decPlaces*=10;
		}
		return {num:num,decPlaces:decPlaces};
	}
	

	this.checkScaledStepMultiplicity = function(num,scalar1,scalar2){
		var shiftedTuple = this.shiftToTens(num);
		var val1 = scalar1*shiftedTuple.num;
		if(val1%5==0)return val1/shiftedTuple.decPlaces;
		var val2 = scalar2*shiftedTuple.num;
		if(val2%5==0)return val2/shiftedTuple.decPlaces;
		return 1;
	}
	
	
	// start dragging
	canvas.addEventListener("mousedown", 
		function(e){
			self.dragged = true;
			self.dx = self.getX(e) - self.x0;
			self.dy = self.getY(e) - self.y0;	
		});	
	
	// update canvas on drag
	canvas.addEventListener("mousemove", 
		function(e){	
			if(self.dragged){
				var newX = self.getX(e) - self.dx;
				var newY = self.getY(e) - self.dy;  

				self.x0 = newX;
				self.y0 = newY;
				self.axisX.y = newY;
				self.axisY.x = newX;
				
			}
			self.refreshCanvas(e);
		});
	 
	 
	 
	// update canvas on scale
	this.scaleGraph = function(e){
		var evt=window.event || e //equalize event object
		var delta=evt.detail? evt.detail*(-120) : evt.wheelDelta //delta returns +120 when wheel is scrolled up, -120 when scrolled down
		var scale = self.getScale();
		var step = self.step;
		var scaleFactor = self.getScaleFactor();
		var direction = -1;
		if(delta<=-120){
			//mousewheel down	
			scale *=(1-scaleFactor);
			direction = 1;
			
			self.nextStep =	self.checkScaledStepMultiplicity(step,1.25,2);
			var stepRatio = self.nextStep/step;

			if(stepRatio == 2 && self.doubleScaleComparator/scale >= 2){
				self.doubleScaleComparator = scale;
				self.quarterScaleComparator = scale;
				self.step = self.nextStep;
			} else if(stepRatio == 1.25 && self.quarterScaleComparator/scale >= 1.25){
				self.quarterScaleComparator = scale;
				self.step = self.nextStep;
			}

		} else {
			//mousewheel up	
			scale *= (1+scaleFactor);
			self.nextStep =	self.checkScaledStepMultiplicity(step,1/2,1/1.25);
			var stepRatio = step/self.nextStep;
			
			if(stepRatio == 1.25 && scale/self.quarterScaleComparator >= 1.25){
				self.quarterScaleComparator = scale;			
				self.step = self.nextStep;
			} else if(stepRatio == 2 && scale/self.doubleScaleComparator >= 2){
				self.doubleScaleComparator = scale;
				self.quarterScaleComparator = scale;
				self.step = self.nextStep;
			}	
		}
		
		self.setScale(scale);
		var dx = self.getX(e) - self.x0;
		var dy = self.getY(e) - self.y0;
		// do nothing if distance is small, 
		// to prevent twitching caused by rapid changes in directions and signs
		var minBound = self.getMetrics().directionalScaleLowerBound;
		if(Math.abs(dx)>minBound && Math.abs(dy)>minBound){
		//increase in direction of scaled unit vector
			var newX = direction*(1+scaleFactor)*dx/Math.abs(dx);
			var newY = direction*(1+scaleFactor)*dy/Math.abs(dy);
			self.x0 += newX;
			self.y0 += newY;
			self.axisX.y += newY;
			self.axisY.x += newX;
		}
		self.refreshCanvas(e);
		//disable default wheel action of scrolling page
		if (evt.preventDefault) 
			evt.preventDefault()
		else
			return false
		 
		}
	 
	// stop dragging
	canvas.addEventListener("mouseup", 
		function(e){
			self.dragged = false;
	});

	canvas.addEventListener("mouseleave", 
		function(e){
			self.dragged = false;
	});
	
	// init canvas object class
	this.f = new Function(this,errDiv,errText);
	var metrics = this.getMetrics();
	this.step = metrics.scale;
	this.nextStep = this.step;
	this.quarterScaleComparator = this.step;
	this.doubleScaleComparator = this.step;
	this.x0 = metrics.centerX;
	this.y0 = metrics.centerY;
	this.axisX = new AxisX(this.y0,this);
	this.axisY = new AxisY(this.x0,this);
	this.axisX.draw();
	this.axisY.draw();
	
}
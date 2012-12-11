/**
 * Layout spans (which need to have display:inline-block) in a tabbed fashion.
 * 
 * Since a graphic says more than a thousand words:
 * 
 * Before:
 * --------------------------------------
 * | VERY_LONGER_SPAN OTHER_SPAN  SPAN1 |
 * | SPAN1 SPAN2 MYSPAN1 SPAN3          |
 * --------------------------------------
 * 
 * After:
 * --------------------------------------
 * | VERY_LONGER_SPAN OTHER_SPAN  SPAN1 |
 * | SPAN1 SPAN2      MYSPAN1     SPAN3 |
 * --------------------------------------
 * 
 * So all spans are aligned to give a nicer look without having to create a table
 * and having to use a fixed number of columns.
 * 
 * The following requirements must be met:
 * - The content to align must be within <span> tags.
 * - These <span> tags must have the CSS property display set to inline-block
 * - The padding-right property may not be used (it is used by this layout)
 */
function doFloatingTabLayout(parent) {
  var parent = $(parent);
  
  // The maximum width the parent gives the content.
  var maxRowWidth = parent.width();

  // Remove own formatting (may not be used by others!) to be idempotent.
  parent.children("br").remove();
  parent.children("span").css("padding-right","");

  // All spans that need placement.
  var spans = parent.children("span");

  // The spans in the first row.
  var firstRow = [];

  // Variables for iterating through all spans.
  var currentRowWidth = 0;
  var currentSpanIndex = 0;
  var currentSpan = undefined;

  // Find spans of first row.
  while (currentSpanIndex < spans.length) {
    currentSpan = $(spans[currentSpanIndex]);
    currentRowWidth += currentSpan.outerWidth(true);
    if (currentRowWidth <= maxRowWidth)
      firstRow.push(currentSpan);
    else
      break;
    currentSpanIndex++;
  }
  
  // If only one row exists stop now.
  if (currentSpanIndex >= spans.length)
    return;

  // At end of first row, prepare for next.
  currentRowWidth = 0;
  currentSpan.before("<br/>");

  var currentFirstRowIndex = 0;

  // Loop through the remaining spans and decide how to align them.
  while (currentSpanIndex < spans.length) {
    currentSpan = $(spans[currentSpanIndex]);
    
    // One of the checks should be enough but this way we are on the safe side.
    if (currentRowWidth >= maxRowWidth ||  currentFirstRowIndex >= firstRow.length) {
      currentRowWidth = 0;
      currentFirstRowIndex = 0;
      currentSpan.before("<br/>");
    }
    
    var currentFirstRowSpan = $(firstRow[currentFirstRowIndex]);

    if (currentSpan.outerWidth(true) < currentFirstRowSpan.outerWidth(true)) {
      // probe if next span(s) would also fit
      
      var spansForColumn = [currentSpan];
      var spansForColumnTotalWidth = currentSpan.outerWidth(true);
      
      for (var temporaryCurrentSpanIndex = currentSpanIndex+1; temporaryCurrentSpanIndex < spans.length; temporaryCurrentSpanIndex++) {
          var temporaryCurrentSpan = $(spans[temporaryCurrentSpanIndex]);
          
          if (spansForColumnTotalWidth + temporaryCurrentSpan.outerWidth(true) <= currentFirstRowSpan.outerWidth(true)) {
            spansForColumn.push(temporaryCurrentSpan);
            spansForColumnTotalWidth  += temporaryCurrentSpan.outerWidth(true);
          } else {
            break;
          }
      }
      
      var difference = currentFirstRowSpan.outerWidth(true) - spansForColumnTotalWidth;
      // TODO: Improve by distributing difference across all spans
      spansForColumn[spansForColumn.length-1].css("padding-right",difference+"px");
      
      currentRowWidth += spansForColumnTotalWidth;
      currentSpanIndex += spansForColumn.length;
      currentFirstRowIndex++;
      
    } else if (currentSpan.outerWidth(true) > currentFirstRowSpan.outerWidth(true)) {
      // use multiple columns
      
      var spanColumnsCount = 0;
      var spanColumnsWidth = 0;
      
      for (var temporaryCurrentFirstRowIndex = currentFirstRowIndex ;
          temporaryCurrentFirstRowIndex < firstRow.length;
          temporaryCurrentFirstRowIndex++) {
          var temporaryCurrentFirstRowSpan = $(firstRow[temporaryCurrentFirstRowIndex]);
          
          spanColumnsCount++;
          spanColumnsWidth += temporaryCurrentFirstRowSpan.outerWidth(true);
          
          if (spanColumnsWidth > currentSpan.outerWidth(true))
            break;
      }
      
      if (spanColumnsWidth > currentSpan.outerWidth(true)) {
        currentSpan.css("padding-right",spanColumnsWidth - currentSpan.outerWidth(true) + "px");
      }
      
      currentRowWidth += spanColumnsWidth;
      currentSpanIndex++;
      currentFirstRowIndex += spanColumnsCount;
    }
    else {
      // simple case: width is exactly the same
      currentRowWidth += currentSpan.outerWidth(true);
      currentSpanIndex++;
      currentFirstRowIndex++;
    }

  }
}

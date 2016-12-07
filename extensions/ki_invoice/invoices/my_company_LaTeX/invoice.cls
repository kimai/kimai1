\NeedsTeXFormat{LaTeX2e}
\ProvidesClass{invoice}[2016/12/07 v1.0 Invoice for kimai]

%%
% This file is part of
% Kimai - Open Source Time Tracking // http://www.kimai.org
% (c) Kimai-Development-Team since 2006
%
% Kimai is free software; you can redistribute it and/or modify
% it under the terms of the GNU General Public License as published by
% the Free Software Foundation; Version 3, 29 June 2007
%
% Kimai is distributed in the hope that it will be useful,
% but WITHOUT ANY WARRANTY; without even the implied warranty of
% MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
% GNU General Public License for more details.
%
% You should have received a copy of the GNU General Public License
% along with Kimai; If not, see <http://www.gnu.org/licenses/>.
%%

%%
% An example LaTeX class template for printing invoices.
%
% @author Gustav Johansson <gustav@neer.se>
%%

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% Load standard document type      %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\LoadClass[10pt,a4paper]{article}
\RequirePackage{fancyhdr}
\RequirePackage{graphicx}
\RequirePackage{longtable}
\RequirePackage{lastpage}
\RequirePackage{hhline}
\RequirePackage{etoolbox}
\RequirePackage{multirow}
\RequirePackage{numprint}
\RequirePackage[table]{xcolor}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% Define template controls         %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%These can be redefined in the template for different languages, etc.
\def\textTitle{\textbf{\textsf{Invoice}}}
\def\textPage{Page}
\def\textDate{Date:}
\def\textDueDate{Due Date:}
\def\textTotal{Total:}
\def\textID{Invoice ID:}
\def\textPhone{Phone: }
\def\textEmail{Email: }
\def\textTotalExVAT{\textbf{Total (excl. VAT)}}
\def\textVAT{\textbf{VAT \vatRate{}\%}}
\def\textGTotal{\textbf{Total (incl. VAT)}}
\def\rulerWidth{0.4pt}
\def\headerOne{Product}
\def\headerTwo{Price}
\def\headerThree{Quantity}
\def\headerFour{Total}
\def\headerColor{gray!50}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% Define commands	           %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%Define product command
\newcommand*{\@products}{}
\def\rowCol{gray!25}
\newcounter{colCount}
\setcounter{colCount}{0}
\newcommand*{\productColor}[4]{%
 \protected@edef\@products{\@products
  \cellcolor{\rowCol}#1\unitOne & \cellcolor{\rowCol}#2\unitTwo &%
  \cellcolor{\rowCol}%
	\ifstrempty{#3}{}{\numprint{#3}\unitThree}%
	& \cellcolor{\rowCol}\numprint{#4}\unitFour\\
 }%
}
\newcommand*{\productNoColor}[4]{%
 \protected@edef\@products{\@products
  #1\unitOne & #2\unitTwo &%
	\ifstrempty{#3}{}{\numprint{#3}\unitThree}%
	& \numprint{#4}\unitFour\\
 }%
}
\newcommand*{\product}[4]{%
 \ifnumequal{\thecolCount}{1}{%
  \productColor{#1}{#2}{#3}{#4}
  \addtocounter{colCount}{-1}%
 }{%
  \productNoColor{#1}{#2}{#3}{#4}
  \addtocounter{colCount}{1}%
 }%
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% Define printing of commands      %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%Print company info
\newcommand{\makecompany}{%
 \noindent
 \companyName\\
 \companyAddress\\
 \textPhone\companyPhone\\
 \textEmail\companyEmail\\
 \par\noindent
 \textID\space\invoiceID\\
 \par\noindent
}

%Print products into a table
\newcommand{\makeproducts}{%
 \nprounddigits{2}
 \begin{longtable}{@{}p{0.5\textwidth}rrr@{}}
  \rowcolor{\headerColor}
   \headerOne&\headerTwo&\headerThree&\headerFour\\
   \@products%
  \hhline{~~--}
  &&\multicolumn{1}{l}{\textTotalExVAT}&\numprint{\total}\space\currency\\
  &&\multicolumn{1}{l}{\textVAT}&\numprint{\vat}\space\currency\\
  &&\multicolumn{1}{l}{\textGTotal}&\numprint{\gtotal}\space\currency
 \end{longtable}
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% Setup the headers and footers    %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\pagestyle{fancy}
\fancyhf{}%Clear all settings
%Set the rulers
\renewcommand{\headrulewidth}{\rulerWidth}
\renewcommand{\footrulewidth}{\rulerWidth}
%Setup header
\fancyhead[L]{%
 \hspace*{0.2cm}\includegraphics[width=4.0cm]{logo.png}%
}
\fancyhead[C]{%
 \raisebox{1.7cm}{%
  \large{\textTitle}%
 }
}
\fancyhead[R]{%
 \raisebox{0.5cm}{%
  \textsf{%
   \begin{tabular}{p{2.0cm}p{3.5cm}}%
    \rowcolor{white}%
    {\small\textDate} & {\small\today}\tabularnewline%
    \rowcolor{white}%
    {\small\textID} & {\small\invoiceID}\tabularnewline%
    \rowcolor{white}%
    \textbf{\small\textDueDate} & \textbf{\duedate}\tabularnewline%
    \rowcolor{white}%
    {\small\textTotal} & {\numprint{\gtotal}\space\currency}%
   \end{tabular}%
  }%
 }%
}
%Setup footer
\fancyfoot[L]{%
 \textsf{%
  \leftOne\\%
  \leftTwo\\%
  \leftThree%
 }%
}
\fancyfoot[C]{%
 \textsf{%
  \centerOne\\%
  \centerTwo\\%
  \centerThree\\%
 }%
}
\fancyfoot[R]{%
 \textsf{%
  \rightOne\\%
  \rightTwo\\%
  \rightThree\\%
  \textPage\space\thepage(\pageref{LastPage})%
 }%
}
\endinput

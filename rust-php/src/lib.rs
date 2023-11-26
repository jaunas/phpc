use std::fmt;

pub struct PhpNumber {
    value: f64,
}

impl PhpNumber {
    pub fn new(value: f64) -> Self {
        PhpNumber { value }
    }

    fn trim_leading_zeroes(&self) -> String {
        let with_precision = format!("{:.13}", self.value);
        return with_precision.trim_end_matches('0').trim_end_matches('.').to_owned();
    }
}

impl fmt::Display for PhpNumber {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        write!(f, "{}", self.trim_leading_zeroes())
    }
}
